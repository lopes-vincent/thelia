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

namespace Thelia\Tools;

use Symfony\Component\HttpFoundation\Request;

class DateTimeFormat
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function getInstance(Request $request)
    {
        return new self($request);
    }

    public function getFormat($output = null)
    {
        $lang = $this->request->getSession()->getLang();

        $format = null;

        if ($lang) {
            switch ($output) {
                case 'date':
                    $format = $lang->getDateFormat();
                    break;
                case 'time':
                    $format = $lang->getTimeFormat();
                    break;
                default:
                case 'datetime':
                    $format = $lang->getDateTimeFormat();
                    break;
            }
        }

        return $format;
    }
}
