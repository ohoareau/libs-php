<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Controller\Base;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractSubDocumentController extends AbstractCrudController
{
    use Traits\Controller\SubDocument\GetSubDocumentControllerTrait;
    use Traits\Controller\SubDocument\BaseSubDocumentControllerTrait;
    use Traits\Controller\SubDocument\FindSubDocumentControllerTrait;
    use Traits\Controller\SubDocument\DeleteSubDocumentControllerTrait;
    use Traits\Controller\SubDocument\UpdateSubDocumentControllerTrait;
    use Traits\Controller\SubDocument\CreateSubDocumentControllerTrait;
}
