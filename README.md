# Nâng cấp cách gọi dữ liệu Geojson trong Php
+ Gọi dữ liệu truy vấn như sau:
	```
		SELECT *, ST_AsGeoJSON(latlng::geometry) AS geojson FROM feat_ktsd_nm
	```