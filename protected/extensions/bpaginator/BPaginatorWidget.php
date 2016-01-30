<?php

class BPaginatorWidget extends CWidget
{
    public $page_url = '';
    public $page_substr = '&page';
    public $kolpages = 0;
    public $page = 1;
    public $css_class = '';
    public $display_count = 10;     // Кол-во номеров страниц в пагинаторе

    public function run()
    {
        $left_delta = floor($this->display_count/2);
        $right_delta = floor($this->display_count/2);

        $start_page = $this->page - $left_delta;
        if($start_page <= 0)
        {
            $start_page = 1;
        }

        $end_page = $this->page + $right_delta;
        if($end_page > $this->kolpages)
        {
            $end_page = $this->kolpages;
        }

        if($this->page > $end_page)
        {
            $end_page = $this->kolpages;
            $start_page = $end_page - ($this->display_count - 1);
            if($start_page <= 0)
            {
                $start_page = 1;
            }
        }

        if($this->page + $right_delta > $end_page)
        {
            $start_page = $end_page - ($this->display_count - 1);
            if($start_page <= 0)
            {
                $start_page = 1;
            }
        }

        if($end_page - $start_page < ($this->display_count - 1))
        {
            $end_page = $start_page + ($this->display_count - 1);
            if($end_page > $this->kolpages)
            {
                $end_page = $this->kolpages;
            }
        }

        /*
        if($end_page - $start_page < ($this->display_count-1))
        {
            $end_page = $start_page + ($this->display_count-1);
        }
        */
//        deb::dump($start_page);
//        deb::dump($end_page);



        $prev_page = $this->page - $this->display_count;
        if($prev_page <= 0)
        {
            $prev_page = 1;
        }

        $next_page = $this->page + $this->display_count;
        if($next_page > $this->kolpages)
        {
            $next_page = $this->kolpages;
        }

        //deb::dump($prev_page);
        //deb::dump($next_page);


        $this->render('index', array(
            'start_page'=>$start_page,
            'end_page'=>$end_page,
            'prev_page'=>$prev_page,
            'next_page'=>$next_page
        ));
    }


    // Заготовка
    public static function shablon()
    {

    }


}
