<div class="services">
    <ul class="auth-services clear">
        <?php
        foreach ($services as $name => $service) {
            echo '<li class="auth-service ' . $service->id . '">';
            $html = '<span class="auth-icon ' . $service->id . '"><i></i></span>';
            $html .= '<span class="auth-title">' . $service->title
                .CHtml::link(
                    'удалить',
                    array($action, 'service' => $name),
                    array('class' => 'auth-link ' . $service->id,)
                )
                .'</span>';
            echo $html;
            echo '</li>';
        }
        ?>
    </ul>
</div>