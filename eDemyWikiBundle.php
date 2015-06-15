<?php

namespace eDemy\WikiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class eDemyWikiBundle extends Bundle
{
    public static function getBundleName($type = null)
    {
        if ($type == null) {

            return 'eDemyWikiBundle';
        } else {
            if ($type == 'Simple') {

                return 'Wiki';
            } else {
                if ($type == 'simple') {

                    return 'wiki';
                }
            }
        }
    }

    public static function eDemyBundle() {

        return true;
    }
}
