<?php
    include "config.php"
?>
<?php
    header('Content-Type: application/json');
    /*---- Thông tin Quận/Huyện ----*/
    $querry_districts = 'SELECT ma_dvhc, "tenDVHC" 
                            FROM "District" WHERE ma_dvhc_cha LIKE ' . "'" . '/82/%' . "'";
    $result_districts = pg_query($tiengiang_db, $querry_districts);
    if (!$result_districts) {
        echo "Không có dữ liệu.\n";
        exit;
    }

    /*** Chuyển định dạng từ Array sang Json ***/
    $data_districts = array();
    while ($row = pg_fetch_assoc($result_districts)) {
        $data_districts[] = $row;
    }
    $jsonData_districts = json_encode($data_districts);
    $original_data_district = json_decode($jsonData_districts, true);
    function gen_Huyen($tenDVHC, $original_data)
    {
        /*** Xử lý ma_dvhc_cha ***/
        $id_huyen = explode("/", $tenDVHC)[1];
        foreach ($original_data as $key => $value) {
            if ($id_huyen == $value["ma_dvhc"]) {
                return $value["tenDVHC"];
            }
        }
    }

    /* Query Geojson */
    $querry_feat = 'SELECT *, ST_AsGeoJSON(latlng::geometry) AS geojson FROM feat_ktsd_nm';

    $result = pg_query($tiengiang_db, $querry_feat);
    if (!$result) {
        echo "Không có dữ liệu.\n";
        exit;
    }

    $geojson = array(
        'type'      => 'FeatureCollection',
        'features'  => array()
    );

    /*** Chuyển định dạng từ Array sang Json ***/
    while ($row = pg_fetch_assoc($result)) {
        $properties = $row;
        # Remove geojson and geometry fields from properties
        unset($properties['geojson']);
        unset($properties['ma_dvhc_cha']);

        # Add new properties
        $properties['huyen'] = gen_Huyen($row["ma_dvhc_cha"], $original_data_district);

        $feature = array(
            'type' => 'Feature',
            'geometry' => json_decode($row['geojson'], true),
            'properties' => $properties
        );
        # Add feature arrays to feature collection array
        array_push($geojson['features'], $feature);
    }

    header('Content-type: application/json');
    echo json_encode($geojson, JSON_NUMERIC_CHECK);
?>
