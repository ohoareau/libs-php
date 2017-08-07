<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Service\ExceptionService;
use Itq\Common\Service\FormatterService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <cto@itiqiti.com>
 *
 * @group services
 * @group services/response
 */
class ResponseServiceTest extends AbstractServiceTestCase
{
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mock('formatterService', FormatterService::class),
            $this->mock('exceptionService', ExceptionService::class),
        ];
    }
}
