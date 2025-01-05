<?php

namespace src\helpers;


use src\App;

/**
 * @author Tim Zapfe
 * @date 07.11.2024
 */
class ResultHelper extends BaseHelper
{
    /**
     * Renders content with json header.
     * @param mixed $data
     * @param int $status
     * @param array $config
     * @return void
     * @author Tim Zapfe
     */
    public static function render(mixed $data, int $status = 200, array $config = []): void
    {
        // set header to json
        self::setHeader($status);

        // merge with default config
        $config = array_merge([
            'translate' => false,
        ], $config);

        // check if data is string
        if (is_string($data) && $config['translate']) {
            // translate string
            $data = App::t($data);
        }

        // check if is object?
        if (is_object($data)) {
            // convert to array
            $data = (array)$data;
        }

        if (!empty($data['message']) && $config['translate']) {
            $data['message'] = App::t($data['message']);
        }

        // return as json encode
        echo json_encode([
            'status'   => $status,
            'response' => $data,
        ]);

        exit();
    }

    /**
     * Echos a given text with additional style in the options array.
     * @param string $text
     * @param array $options
     * @return void
     * @author Tim Zapfe
     * @date 18.11.2024
     */
    public static function echo(string $text, array $options = []): void
    {
        $options = array_merge([
            'color'            => '#000000',
            'background-color' => 'transparent',
            'padding'          => '5px',
            'margin'           => '5px',
            'font-size'        => '16px',
            'font-weight'      => 'bold'
        ], $options);

        $html = '<p style="';

        foreach ($options as $key => $value) {
            $html .= $key . ': ' . $value . '; ';
        }

        $html .= '">' . $text . '</p>';

        echo $html;
    }

    /**
     * Adds a table row with a given text. To output proper table, use ResultHelper::switchView(true)
     * @param string $text
     * @param string $type
     * @return void
     * @author Tim Zapfe
     * @date 18.11.2024
     */
    public static function log(string $text, string $type = 'info'): void
    {
        $date = date('Y.m.d H:i:s');

        switch ($type) {
            case 'error':
                $background = 'transparent';
                $color = 'red';
                break;
            case 'success':
                $background = 'transparent';
                $color = 'green';
                break;
            case 'warn':
                $background = 'transparent';
                $color = 'orange';
                break;
            default:
                $color = 'black';
                $background = 'transparent';
        }

        echo '<tr>';
        echo '<td>' . $date . '</td>';
        echo '<td style="background-color: ' . $background . '; color: ' . $color . ';">' . $text . '</td>';
        echo '</tr>';

    }

    /**
     * Opens and closes a table.
     * @param bool $open
     * @return void
     * @author Tim Zapfe
     * @date 18.11.2024
     */
    public static function switchView(bool $open = false): void
    {
        echo '<style>
        table {
        width: 100%;
            & thead th {
                background-color: #4CAF50; / *Grün für Tabellenkopf* /
            }
            & tbody & tr:nth-child(odd) {
                background-color: #f2f2f2; / *Hellgrauer Hintergrund für ungerade Zeilen* /
            }
            & tbody & tr:nth-child(even) {
                background-color: #ffffff; / *Weißer Hintergrund für gerade Zeilen* /
            }
        }
            </style>';

        if ($open) {
            echo '<table><tr><th style="width: 200px;">Timestamp</th><th>Log</th></tr>';
        } else {
            echo '</table>';
            die();
        }
    }

    /**
     * Set the header as JSON
     * @param int $status
     * @return void
     * @author Tim Zapfe
     */
    private static function setHeader(int $status): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
    }
}