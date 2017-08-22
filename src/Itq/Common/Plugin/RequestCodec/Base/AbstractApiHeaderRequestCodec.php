<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\RequestCodec\Base;

use DateTime;
use Exception;
use DateInterval;
use Itq\Common\Traits;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractApiHeaderRequestCodec extends AbstractRequestCodec
{
    use Traits\Helper\Date\DateToStringTrait;
    /**
     * @param string $headerKey
     * @param array  $requiredHeaderKeys
     */
    public function __construct($headerKey, array $requiredHeaderKeys = [])
    {
        $this->setHeaderKey($headerKey);

        foreach ($requiredHeaderKeys as $key => $error) {
            $this->addRequiredHeaderKey($key, $error);
        }
    }
    /**
     * @return string
     */
    public function getHeaderKey()
    {
        return $this->getParameter('headerKey');
    }
    /**
     * @param string $headerKey
     *
     * @return $this
     */
    public function setHeaderKey($headerKey)
    {
        return $this->setParameter('headerKey', $headerKey);
    }
    /**
     * @param Request $request
     * @param array   $options
     *
     * @return array
     */
    public function decode(Request $request, array $options = [])
    {
        return $this->parseHeaderValue($request->headers->get(strtolower($this->getHeaderKey())));
    }
    /**
     * @param string $headerKey
     * @param string $error
     *
     * @return $this
     */
    public function addRequiredHeaderKey($headerKey, $error)
    {
        return $this->setArrayParameterKey('requiredHeaderKeys', $headerKey, $error);
    }
    /**
     * @return array
     */
    public function getRequiredHeaderKeys()
    {
        return $this->getArrayParameter('requiredHeaderKeys');
    }
    /**
     * @param Request $request
     * @param array   $data
     * @param array   $options
     *
     * @return array
     */
    public function encode(Request $request, array $data = [], array $options = [])
    {
        $parts = $this->parseHeader($request);

        $this->processEncoding($parts, $options);

        return $this->buildExpirableHeader($parts);
    }
    /**
     * @param array $parts
     * @param array $options
     *
     * @throws Exception
     */
    protected function processEncoding(array $parts, array $options = [])
    {
        unset($parts, $options);

        throw $this->createDeniedException('Encoding of this type of api header is not supported');
    }
    /**
     * @param string    $id
     * @param \DateTime $expire
     * @param string    $token
     *
     * @return string
     */
    protected function buildExpirableTokenHeaderValue($id, \DateTime $expire, $token)
    {
        return sprintf('id: %s, expire: %s, token: %s', $id, $this->convertDateToString($expire), $token);
    }
    /**
     * @param Request $request
     *
     * @return array
     */
    protected function parseHeader(Request $request)
    {
        $this->checkRequiredHeaderKeys($request);

        $headers = $request->headers->all();

        if (!isset($headers[strtolower($this->getHeaderKey())])) {
            $headers[strtolower($this->getHeaderKey())] = null;
        }

        return $this->parseHeaderValue($headers[strtolower($this->getHeaderKey())]);
    }
    /**
     * @param string $header
     *
     * @return array
     */
    protected function parseHeaderValue($header)
    {
        if (is_array($header)) {
            $header = array_shift($header);
        }

        $parts = [];

        foreach (preg_split("/\\s*,\\s*/", trim($header)) as $t) {
            if (false === strpos($t, ':')) {
                break;
            }
            list($key, $value) = explode(':', $t, 2);
            $key   = trim($key);
            $value = trim($value);
            if ($this->isNonEmptyString($value)) {
                $parts[$key] = $value;
            }
        }

        return $parts + $this->getDefaultValues();
    }
    /**
     * @return array
     */
    protected function getDefaultValues()
    {
        return ['id' => null];
    }
    /**
     * @param Request $request
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function checkRequiredHeaderKeys(Request $request)
    {
        foreach ($this->getRequiredHeaderKeys() as $headerKey => $error) {
            if (!$request->headers->has(strtolower($headerKey))) {
                throw $this->createAuthorizationRequiredException($error);
            }
        }

        return $this;
    }
    /**
     * @param string   $value
     * @param DateTime $expire
     *
     * @return string
     */
    protected function stamp($value, DateTime $expire)
    {
        return base64_encode(sha1(sprintf('%s%s%s', $value, $this->convertDateToString($expire), $this->getExtraStampedString())));
    }
    /**
     * @param array         $data
     * @param DateTime|null $expire
     *
     * @return array
     */
    protected function buildExpirableHeader(array $data, DateTime $expire = null)
    {
        if (null === $expire) {
            $now    = new DateTime();
            $expire = $now->add(new DateInterval('P1D'));
        }

        return [$this->getHeaderKey() => $this->buildExpirableTokenHeaderValue($data['id'], $expire, $this->stamp($data['id'], $expire))];
    }
    /**
     * @return string
     */
    protected function getExtraStampedString()
    {
        return null;
    }
}
