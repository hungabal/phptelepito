<?php
if (!file_exists("public/db.conf.php")) {
    header('Location: http://localhost/dashboard/telepito/install/installer.php?step=1&hiba=0');
}

echo "Tartalom";