<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Exception;
use Google_Client;
use Itq\Common\Traits;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GoogleService
{
    use Traits\ServiceTrait;
    /**
     * @param string $application
     * @param string $client
     * @param string $project
     * @param string $secret
     * @param string $tokenFilePath
     *
     * @throws \Exception
     */
    public function __construct($application, $client, $project, $secret, $tokenFilePath)
    {
        if (!class_exists('Google_Service_Drive')) {
            throw $this->createRequiredException('Google Drive API SDK required');
        }

        $config = [
            'applicationName' => $application,
            'scopes'          => implode(' ', [Google_Service_Drive::DRIVE]),
            'installed'       => [
                'client_id'                   => $client,
                'project_id'                  => $project,
                'auth_uri'                    => 'https://accounts.google.com/o/oauth2/auth',
                'token_uri'                   => 'https://accounts.google.com/o/oauth2/token',
                'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
                'client_secret'               => $secret,
                'redirect_uris'               => ['urn:ietf:wg:oauth:2.0:oob', 'http://localhost'],
            ],
        ];
        $this->setParameter('tokenFilePath', $tokenFilePath);
        $this->setParameter('tokenAvailable', false);
        $this->setGoogleClient($this->loadGoogleClient($config));
        $this->setGoogleDriveService(new Google_Service_Drive($this->getGoogleClient()));
    }
    /**
     * @param Google_Client $googleClient
     */
    public function setGoogleClient(Google_Client $googleClient)
    {
        $this->setService('googleClient', $googleClient);
    }
    /**
     * @return Google_Client
     */
    public function getGoogleClient()
    {
        return $this->getService('googleClient');
    }
    /**
     * @param Google_Service_Drive $googleDriveService
     */
    public function setGoogleDriveService(Google_Service_Drive $googleDriveService)
    {
        $this->setService('googleDriveService', $googleDriveService);
    }
    /**
     * @return Google_Service_Drive
     */
    public function getGoogleDriveService()
    {
        return $this->getService('googleDriveService');
    }
    /**
     * @param string $parent
     * @param string $path
     * @param string $content
     * @param array  $options
     *
     * @return $this
     */
    public function writeFileByPath($parent, $path, $content, array $options = [])
    {
        $this->createOrUpdateByName(
            $this->ensureDirectoryExist($parent, dirname($path), $options),
            basename($path),
            $content,
            $options
        );

        return $this;
    }
    /**
     * @param string $parent
     * @param string $name
     * @param string $content
     * @param array  $options
     *
     * @return string
     */
    public function createOrUpdateByName($parent, $name, $content, array $options = [])
    {
        $file = $this->findOneFileByName($parent, $name, $options);

        if (!$file) {
            $file = $this->createFile(
                $parent,
                [
                    'name' => $name,
                    'content' => $content,
                    'contentType' => isset($options['contentType']) ? $options['contentType'] : null,
                ]
            );
        } else {
            $this->updateFile($file->getId(), $content, $options + ['contentType' => $file->getMimeType()]);
        }

        return $file->getId();
    }
    /**
     * @param string $id
     * @param string $content
     * @param array  $options
     *
     * @return Google_Service_Drive_DriveFile
     */
    public function updateFile($id, $content, array $options = [])
    {
        return $this->getGoogleDriveService()->files->update(
            $id,
            new Google_Service_Drive_DriveFile(),
            [
                'data'       => $content,
                'mimeType'   => isset($options['contentType']) ? $options['contentType'] : null,
                'uploadType' => 'multipart',
                'fields'     => 'id, modifiedTime',
            ]
        );
    }
    /**
     * @param string $parent
     * @param string $name
     * @param array  $options
     *
     * @return Google_Service_Drive_DriveFile|null
     */
    public function findOneFileByName($parent, $name, array $options = [])
    {
        $found = null;

        foreach ($this->getGoogleDriveService()->files->listFiles(['q' => sprintf('name = \'%s\' and \'%s\' in parents and trashed = false', $name, $parent)]) as $file) {
            $found = $file;
            break;
        }

        unset($options);

        return $found;
    }
    /**
     * @param string $parent
     * @param string $path
     * @param array  $options
     *
     * @return Google_Service_Drive_DriveFile
     */
    public function getFileByPath($parent, $path, array $options = [])
    {
        if ('.' === $path) {
            return $this->getFile($parent);
        }

        return $this->getFileByPath($this->getFileByPath($parent, dirname($path), $options)->id, basename($path), $options);
    }
    /**
     * @param string $parent
     * @param string $path
     * @param array  $options
     *
     * @return bool
     */
    public function hasFileByPath($parent, $path, array $options = [])
    {
        if ('.' === $path) {
            return true;
        }

        return $this->hasFileByPath($parent, dirname($path), $options)
        && $this->hasFileByPath($this->getFileByPath($parent, dirname($path), $options)->id, basename($path), $options)
            ;
    }
    /**
     * @param string $parent
     * @param string $path
     * @param array  $options
     *
     * @return $this
     */
    public function removeFileByPath($parent, $path, array $options = [])
    {
        $this->deleteFile($this->getFileByPath($parent, $path, $options)->id, $options);

        return $this;
    }
    /**
     * @param string $parent
     * @param string $name
     * @param array  $options
     *
     * @return string
     */
    public function ensureDirectoryExist($parent, $name, array $options = [])
    {
        if ('.' === $name) {
            return $parent;
        }

        $tokens    = explode('/', $name);
        $lastToken = array_pop($tokens);

        foreach ($tokens as $token) {
            if (!empty($token)) {
                $parent = $this->ensureDirectoryExist($parent, $token, $options);
            }
        }

        $dir = $this->findOneDirectoryByName($parent, $lastToken, $options);

        if (!$dir) {
            $dir = $this->createDirectory($parent, $lastToken, $options);
        }

        return $dir->id;
    }
    /**
     * @param string $parent
     * @param string $name
     * @param array  $options
     *
     * @return Google_Service_Drive_DriveFile|null
     */
    public function findOneDirectoryByName($parent, $name, array $options = [])
    {
        $found = null;
        foreach ($this->getGoogleDriveService()->files->listFiles(['q' => sprintf('name = \'%s\' and mimeType = \'application/vnd.google-apps.folder\' and \'%s\' in parents and trashed = false', $name, $parent)]) as $file) {
            $found = $file;
            break;
        }
        unset($options);

        return $found;
    }
    /**
     * @param string $parent
     * @param string $name
     * @param array  $options
     *
     * @return Google_Service_Drive_DriveFile
     */
    public function createDirectory($parent, $name, array $options = [])
    {
        unset($options);

        return $this->getGoogleDriveService()->files->create(
            new Google_Service_Drive_DriveFile([
                'name'     => $name,
                'parents'  => [$parent],
                'mimeType' => 'application/vnd.google-apps.folder',
            ]),
            ['fields' => 'id']
        );
    }
    /**
     * @param string $parentId
     * @param array  $data
     *
     * @return Google_Service_Drive_DriveFile
     */
    public function createFile($parentId, array $data)
    {
        return $this->getGoogleDriveService()->files->create(
            new Google_Service_Drive_DriveFile(['name' => $data['name'], 'parents' => [$parentId]]),
            [
                'data'       => $data['content'],
                'mimeType'   => $data['contentType'],
                'uploadType' => 'multipart',
                'fields'     => 'id',
            ]
        );
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return $this
     */
    public function deleteFile($id, array $options = [])
    {
        $this->getGoogleDriveService()->files->delete($id, []);

        unset($options);

        return $this;
    }
    /**
     * @param string $id
     *
     * @return Google_Service_Drive_DriveFile
     */
    public function getFile($id)
    {
        return $this->getGoogleDriveService()->files->get($id, ['fields' => 'id']);
    }

    /**
     * @param Google_Client $client
     * @return boolean
     */
    public function refreshTokenIfNeeded(Google_Client $client)
    {
        $credentialsPath = $this->getParameter('tokenFilePath');

        if (!file_exists($credentialsPath)) {
            return false;
        }

        $accessToken = json_decode(file_get_contents($credentialsPath), true);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }

        return true;
    }
    /**
     * @param array $config
     * @return Google_Client
     *
     * @throws Exception
     */
    protected function loadGoogleClient(array $config)
    {
        $client = new Google_Client();

        $client->setApplicationName($config['applicationName']);
        $client->setScopes($config['scopes']);
        $client->setAuthConfig($config);
        $client->setAccessType('offline');

        $this->setParameter('tokenAvailable', $this->refreshTokenIfNeeded($client));

        return $client;
    }
}
