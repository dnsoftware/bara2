<?php

class PProptypes {

    public static function displayPropsSprav($rp_id, $prop_types_params_row)
    {
        PropsSprav::model()->findAll(
            array(
                'select'=>'*',
                'condition'=>'rp_id = '.$rp_id,
                'order'=>'selector, sort_number',
                //'limit'=>'10'
            )
        );

        switch ($prop_types_params_row->maybe_count)
        {
            case "one":

            break;

            case "many":
                echo "121212";
            break;
        }
    }

} 