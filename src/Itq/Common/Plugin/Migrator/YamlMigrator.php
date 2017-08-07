<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Migrator;

use Symfony\Component\Yaml\Yaml;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class YamlMigrator extends Base\AbstractMigrator
{
    /**
     * @param string $path
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function upgrade($path, $options = [])
    {
        unset($options);

        $data = Yaml::parse(file_get_contents($path));

        if (isset($data['db']) && is_array($data['db'])) {
            foreach ($data['db'] as $type => $items) {
                $officialType = $type;
                if (substr_count($type, '_') > 1) {
                    $officialType = substr($type, 0, strpos($type, '_', strpos($type, '_') + 1));
                }
                $service = $this->getContainer()->get(sprintf('app.%s', str_replace('_', '.', $officialType)));
                foreach ($items as $id => $item) {
                    $propertyName = null;
                    if (!is_numeric($id) && !isset($item['id'])) {
                        if (false !== strpos($id, '/')) {
                            list($propertyName, $propertyValue) = explode('/', $id, 2);
                            $item[$propertyName] = $propertyValue;
                        } else {
                            $item['id'] = $id;
                        }
                    }
                    $parentTypes = [];
                    if (false !== strpos($type, '_')) {
                        $parentTypes = explode('_', $type);
                        array_pop($parentTypes);
                    }
                    switch (count($parentTypes)) {
                        case 0:
                            if (isset($item['id']) && 'index' === $item['id']) {
                                unset($item['id']);
                                $indexList = [];
                                $indexListString = [];
                                foreach ($item as $kkk => $vvv) {
                                    $indexList[] = is_string($vvv) ? $vvv : $vvv['field'];
                                    $indexListString[] = is_string($vvv) ? $vvv : (is_array($vvv['field']) ? join('+', array_keys($vvv['field'])) : $vvv['field']);
                                }
                                $this->log(sprintf("  + @index %s (%s)", $type, join(',', $indexListString)), 'info');
                                $service->getRepository()->createIndexes($item);
                                continue;
                            }
                            if (!isset($item['id'])) {
                                if (isset($propertyName)) {
                                    $propertyValue = $item[$propertyName];
                                    if ($service->hasBy($propertyName, $item[$propertyName])) {
                                        unset($item[$propertyName]);
                                        $this->log(sprintf("  . %s %s=%s", $type, $propertyName, $propertyValue), 'info');
                                        $service->updateBy($propertyName, $propertyValue, $item);
                                    } else {
                                        $this->log(sprintf("  + %s %s=%s", $type, $propertyName, $propertyValue), 'info');
                                        $service->create($item);
                                    }
                                } else {
                                    $this->log(sprintf("  + %s %s", $type, is_numeric($id) ? reset($item) : (string) $id), 'info');
                                    $service->create($item);
                                }
                            } elseif (!$service->has($item['id'])) {
                                $this->log(sprintf("  + %s %s", $type, is_numeric($id) ? reset($item) : (string) $id), 'info');
                                $service->create($item);
                            } else {
                                $itemId = $item['id'];
                                $this->log(sprintf("  . %s %s", $type, $itemId), 'info');
                                unset($item['id']);
                                $service->update($itemId, $item);
                                $item['id'] = $itemId;
                            }
                            break;
                        case 1:
                            if (!isset($item['id'])) {
                                throw $this->createRequiredException('Missing sub type id');
                            }
                            if (false === strpos($item['id'], '_')) {
                                throw $this->createRequiredException("Missing parent id for '%s'", $item['id']);
                            }
                            list($itemId, $subItemId) = explode('_', $item['id'], 2);
                            if (!$service->has($itemId, $subItemId)) {
                                $item['id'] = $subItemId;
                                $service->create($itemId, $item);
                            } else {
                                unset($item['id']);
                                $service->update($itemId, $subItemId, $item);
                            }
                            break;
                        default:
                            throw $this->createUnexpectedException("Unsupported type '%s' (too much parent levels)", $type);
                    }
                }
            }
        }

        return $this;
    }
}
