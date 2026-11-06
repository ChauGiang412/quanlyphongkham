<?php
require_once 'config/Database.php';
require_once 'models/GoiKham.php';


class GoiKhamController {
    public function detail() {
        $ma_goi = $_GET['id'] ?? 0;


        $db = (new Database())->getConnection();
        $goiKhamModel = new GoiKham($db);
        $goiKham = $goiKhamModel->getGoiKhamById($ma_goi);


        if (!$goiKham) {
            header("Location: index.php?act=home");
            exit();
        }


        require_once 'views/header.php';
        require_once 'views/chi_tiet_goi_kham.php';
        require_once 'views/footer.php';
    }
}

