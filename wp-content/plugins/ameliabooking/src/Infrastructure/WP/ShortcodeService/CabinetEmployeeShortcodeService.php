<?php
/**
 * @copyright © TMS-Plugins. All rights reserved.
 * @licence   See LICENCE.md for license details.
 */

namespace AmeliaBooking\Infrastructure\WP\ShortcodeService;

/**
 * Class CabinetEmployeeShortcodeService
 *
 * @package AmeliaBooking\Infrastructure\WP\ShortcodeService
 */
class CabinetEmployeeShortcodeService extends AmeliaShortcodeService
{
    /**
     * @return string
     */
    public static function shortcodeHandler($atts)
    {
        $atts = shortcode_atts(
            [
                'trigger'      => '',
                'counter'      => self::$counter,
                'appointments' => null,
                'events'       => null
            ],
            $atts
        );

        self::prepareScriptsAndStyles();

        ob_start();
        include AMELIA_PATH . '/view/frontend/cabinet-employee.inc.php';
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
