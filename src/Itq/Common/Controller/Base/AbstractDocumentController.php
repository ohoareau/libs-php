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
abstract class AbstractDocumentController extends AbstractCrudController
{
    use Traits\Controller\Document\GetDocumentControllerTrait;
    use Traits\Controller\Document\BaseDocumentControllerTrait;
    use Traits\Controller\Document\FindDocumentControllerTrait;
    use Traits\Controller\Document\DeleteDocumentControllerTrait;
    use Traits\Controller\Document\UpdateDocumentControllerTrait;
    use Traits\Controller\Document\CreateDocumentControllerTrait;
}
