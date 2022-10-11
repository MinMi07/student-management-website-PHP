<?php
    require_once "./LogIn/sql.php";
    $sql = new SQL();

    $namhoc_truoc = '';
    $namhoc_hientai = '';    
    $tuan_hientai = '1';
    $hocky_hientai = '1';
    

    //Năm học hiện tại
    $data = $sql->getdata("SELECT * from namhoc where NamHoc like '%".date('Y')."%'");
    $bd1 = '';
    $bd2 = '';
    $i=0;
    while($a = $data->fetch_assoc()){
        $query = "SELECT * from namhoc_hocky where NamHoc = '".$a['NamHoc']."' and HocKy = '1'";
        $mang[$i] = $sql->getdata($query)->fetch_assoc()['NgayBD'];
        $i++;
        $namhoc_hientai = $a['NamHoc'];
    }
    if($i==2){
        if(strtotime(date('Y/m/d')) > strtotime($mang[0]) && strtotime(date('Y/m/d')) > strtotime($mang[1]))
        {
            if(strtotime($mang[0]) > strtotime($mang[1])){
                $bd1 = $mang[1];
                $bd2 = $mang[0];
            }
            else{
                $bd1 = $mang[0];
                $bd2 = $mang[1];
            }
            $namhoc_hientai = $sql->getdata("SELECT * from namhoc_hocky where HocKy = '1' and NgayBD = '$bd2'")->fetch_assoc()['NamHoc'];
            $namhoc_truoc =  $sql->getdata("SELECT * from namhoc_hocky where HocKy = '1' and NgayBD = '$bd1'")->fetch_assoc()['NamHoc'];
        }
        else{
            if(strtotime($mang[0]) > strtotime($mang[1])){
                $bd1 = $mang[1];
                $bd2 = $mang[0];
            }
            else{
                $bd1 = $mang[0];
                $bd2 = $mang[1];
            }
            $namhoc_hientai = $sql->getdata("SELECT * from namhoc_hocky where HocKy = '1' and NgayBD = '$bd1'")->fetch_assoc()['NamHoc'];
            $namhoc_truoc =  (date('Y')-2)."-".(date('Y')-1);
        }
    }
    else{ 
        $namhoc_truoc = (date('Y')-2)."-".(date('Y')-1);
    }

    $tinhtrang_hs = $sql->getdata("SELECT * from hocsinh where MaHS = '".$_SESSION['userhs']."'")->fetch_assoc()['TinhTrang'];
    if($tinhtrang_hs == "tốt nghiệp"){
        $namhoc_hientai = $sql->getdata("SELECT * FROM `lophoc` inner join lophoc_hocsinh on lophoc_hocsinh.MaLop = lophoc.MaLop where MaHS = '".$_SESSION['userhs']."' ORDER by NamHoc DESC")->fetch_assoc()['NamHoc'];
    }
        
    //Năm học sau
    $namhientai = date('Y',strtotime($sql->getdata("SELECT NgayKT from namhoc_hocky where NamHoc = '".$namhoc_hientai."' and HocKy = '2'")->fetch_assoc()['NgayKT']));
    $namhoc_sau = $namhientai."-".($namhientai+1);


    //Học kỳ hiện tại
    $query = "SELECT * from namhoc_hocky where NamHoc = '".$namhoc_hientai."' and HocKy = '1'";
    
    $data = $sql->getdata($query);
    $today = date('Y/m/d');
    if($data->num_rows >0){
        while($a = $data->fetch_assoc()){
            if(strtotime($today) >= strtotime($a['NgayBD']) && strtotime($today) <= strtotime($a['NgayKT']) )
                $hocky_hientai = '1';
            else
                $hocky_hientai = '2';
        }
    }


    //Tuần hiện tại
    $tuan1 = (strtotime('2021/12/27')-strtotime('2021/9/13'))/15;
    $ngaybd = $sql->getdata("SELECT * from namhoc_hocky where NamHoc = '$namhoc_hientai' and HocKy = '$hocky_hientai'")->fetch_assoc()['NgayBD'];
    $a = (int)((strtotime(date('Y/m/d'))-strtotime($ngaybd))/$tuan1) + 1;
    if($a <= 15 && $a >=1){
        $tuan_hientai = $a;
    }

    $khoi_hientai = $sql->getdata("SELECT Khoi FROM `lophoc_hocsinh` INNER JOIN lophoc ON lophoc_hocsinh.MaLop = lophoc.MaLop where MaHS = '".$_SESSION['userhs']."' and NamHoc = '".$namhoc_hientai."'")->fetch_assoc()['Khoi'];
    $tenhs_hientai = $sql->getdata("SELECT TenHS from hocsinh where MaHS = '".$_SESSION['userhs']."'")->fetch_assoc()['TenHS'];

    $query_lophoc = "SELECT lophoc_hocsinh.MaLop from hocsinh inner join lophoc_hocsinh on hocsinh.MaHS = lophoc_hocsinh.MaHS inner join lophoc on lophoc.MaLop = lophoc_hocsinh.MaLop where hocsinh.MaHS = '".$_SESSION['userhs']."' and NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $malop = $data->fetch_assoc()['MaLop'];
    
    $query_lophoc = "SELECT TenLop from hocsinh inner join lophoc_hocsinh on hocsinh.MaHS = lophoc_hocsinh.MaHS inner join lophoc on lophoc.MaLop = lophoc_hocsinh.MaLop where hocsinh.MaHS = '".$_SESSION['userhs']."' and NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $solop = $data->fetch_assoc()['TenLop'];

    $query_lophoc = "SELECT TenGV from hocsinh inner join lophoc_hocsinh on hocsinh.MaHS = lophoc_hocsinh.MaHS inner join lophoc on lophoc.MaLop = lophoc_hocsinh.MaLop inner join giaovien on lophoc.MaGV = giaovien.MaGV where hocsinh.MaHS = '".$_SESSION['userhs']."' and NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $sogv = $data->fetch_assoc()['TenGV'];

    $query_lophoc = "SELECT SiSo from hocsinh inner join lophoc_hocsinh on hocsinh.MaHS = lophoc_hocsinh.MaHS inner join lophoc on lophoc.MaLop = lophoc_hocsinh.MaLop inner join giaovien on lophoc.MaGV = giaovien.MaGV where hocsinh.MaHS = '".$_SESSION['userhs']."' and NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $sohs = $data->fetch_assoc()['SiSo'];

    $query_lophoc = "SELECT count(MaLop) as So from LopHoc where TenLop like '%A%'";
    $data = $sql->getdata($query_lophoc);
    $solopA = $data->fetch_assoc()['So'];


    $query_lophoc = "SELECT count(MaLop) as So from LopHoc where TenLop like '%B%'";
    $data = $sql->getdata($query_lophoc);
    $solopB = $data->fetch_assoc()['So'];

    $query_lophoc = "SELECT count(MaLop) as So from LopHoc where TenLop like '%C%'";
    $data = $sql->getdata($query_lophoc);
    $solopC = $data->fetch_assoc()['So'];

    $query_lophoc = "SELECT count(MaLop) as So from LopHoc where TenLop like '%D%'";
    $data = $sql->getdata($query_lophoc);
    $solopD = $data->fetch_assoc()['So'];

    $query_lophoc = "SELECT count(lophoc_hocsinh.MaHS) as So from lophoc_hocsinh inner join LopHoc on lophoc_hocsinh.MaLop = LopHoc.MaLop inner join hocsinh on lophoc_hocsinh.MaHS = hocsinh.MaHS where TenLop like '%6%' and lophoc.NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $sohs6 = $data->fetch_assoc()['So'];

    $query_lophoc = "SELECT count(lophoc_hocsinh.MaHS) as So from lophoc_hocsinh inner join LopHoc on lophoc_hocsinh.MaLop = LopHoc.MaLop inner join hocsinh on lophoc_hocsinh.MaHS = hocsinh.MaHS where TenLop like '%7%' and lophoc.NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $sohs7 = $data->fetch_assoc()['So'];

    $query_lophoc = "SELECT count(lophoc_hocsinh.MaHS) as So from lophoc_hocsinh inner join LopHoc on lophoc_hocsinh.MaLop = LopHoc.MaLop inner join hocsinh on lophoc_hocsinh.MaHS = hocsinh.MaHS where TenLop like '%8%' and lophoc.NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $sohs8 = $data->fetch_assoc()['So'];


    $query_lophoc = "SELECT count(lophoc_hocsinh.MaHS) as So from lophoc_hocsinh inner join LopHoc on lophoc_hocsinh.MaLop = LopHoc.MaLop inner join hocsinh on lophoc_hocsinh.MaHS = hocsinh.MaHS where TenLop like '%9%' and lophoc.NamHoc = '$namhoc_hientai'";
    $data = $sql->getdata($query_lophoc);
    $sohs9 = $data->fetch_assoc()['So'];
?>