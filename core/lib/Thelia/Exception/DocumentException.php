<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thelia\Exception;

use Thelia\Log\Tlog;

class DocumentException extends \RuntimeException
{
    public function __construct($message, $code = null, $previous = null)
    {
        Tlog::getInstance()->addError($message);

        parent::__construct($message, $code, $previous);
    }
}
