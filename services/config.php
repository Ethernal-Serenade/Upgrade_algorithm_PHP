<?php
$tiengiang_db = pg_connect("host=localhost
            port=5432
            dbname=tnn_tiengiang
            user=postgres
            password=0888365051"
);
if (!$tiengiang_db) {
    echo "Kết nối thất bại.\n";
    exit;
}
