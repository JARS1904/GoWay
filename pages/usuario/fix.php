<?php
$content = file_get_contents('c:\\xampp\\htdocs\\GoWay\\pages\\usuario\\route_selected_screen.php');
$lines = explode("\n", $content);
$new_lines = array_slice($lines, 0, 178);
$new_content = implode("\n", $new_lines);

$append = '
    <script>
        // Configuración de API
        const API_BASE_URL = window.location.origin;
        const API_URL = `${API_BASE_URL}/GoWay/api/routes_api.php`;
        const FAVORITES_URL = `${API_BASE_URL}/GoWay/api/favorites_routes_api.php`;
        const ID_USUARIO = <?php echo isset($_SESSION[\'id\']) ? $_SESSION[\'id\'] : 0; ?>;
    </script>
    
    <!-- Lógica principal de la vista -->
    <script src="../../assets/js/route_selected_screen.js"></script>

    <?php 
    // Paneles modulares
    require_once \'../../components/profile_panel.php\';
    require_once \'../../components/report_modal.php\';
    
    $hide_send_notification = true;
    require_once \'../../components/notifications_panel.php\'; 
    
    require_once \'../../components/favorites_panel.php\';
    ?>

</body>
</html>
';

file_put_contents('c:\\xampp\\htdocs\\GoWay\\pages\\usuario\\route_selected_screen.php', $new_content . "\n" . $append);
echo "Fixed!";
