
<?php
$title = "Διεγραμμένα Υλικά";
require('../templates/header.inc.php');
check_session();
?>
            <!-- Table Start -->
            <div class="container-fluid pt-4 px-4 overflow-auto custom-table-border">
                <div class="row g-4" >
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light rounded h-100 p-4 auto-width">
                            <h6 class="mb-4">ΔΙΕΓΡΑΜΜΕΝΑ ΥΛΙΚΑ</h6>

                            <!-- Search Bar Start -->
                            <div id="search">
                                <form method="post" action="" class="form-inline">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control mb-3" name="barcode" placeholder="Barcode">
                                        <input type="text" class="form-control mb-3" name="typos" placeholder="Τύπος">

                                        <select class="form-select mb-3" name="id_katigoria_prod">
                                            <option value="">Κατηγορία</option>
                                            <?php dropdown_menus_by_table('katigoria_prod', $dbcon); ?>
                                        </select>

                                        <select class="form-select mb-3" name="id_kataskeuastis">
                                            <option value="">Κατασκευαστής</option>
                                            <?php dropdown_menus_by_table('kataskeuastis', $dbcon); ?>
                                        </select>

                                        <input type="text" class="form-control mb-3" name="merida" placeholder="Μερίδα">

                                        <select class="form-select mb-3" name="leitourgiko">
                                            <option selected>Λειτουργικό</option>
                                            <option value="1">Ναι</option>
                                            <option value="0">Όχι</option>
                                        </select>

                                        <select class="form-select mb-3" name="xreomeno">
                                            <option selected>Χρεωμένο</option>
                                            <option value="1">Ναι</option>
                                            <option value="0">Όχι</option>
                                        </select>

                                        <select class="form-select mb-3" id="id_tmima" name="id_tmima">
                                            <option value="">Τμήμα</option>
                                            <?php $id_tmima=null; dropdown_menus_by_table('tmima', $dbcon, $id_tmima, $_SESSION['id_monada'])  ?>
                                        </select> 

                                        <select class="form-select mb-3" name="id_diktyo">
                                            <option value="">Δίκτυο</option>
                                            <?php dropdown_menus_by_table('diktyo', $dbcon); ?>
                                        </select>
                                    </div>
                                    
                                    <p><button type="submit" class="btn btn-primary">Αναζήτηση με χρήση των παραπάνω φίλτρων</button></p>
                                </form>
                            </div>
                            <!-- Search Bar End -->

                            <table class="table table-striped text-center custom-table align-middle" id="downloadable" style="display: block; max-height: 350px; overflow-y: scroll">
                                <thead style="position: sticky; top:0">
                                    <tr class="bg-light">
                                        <th scope="col">#</th>
                                        <th scope="col">BARCODE</th>
                                        <th scope="col">ΤΥΠΟΣ</th>
                                        <th scope="col">ΚΑΤΗΓΟΡΙΑ</th>
                                        <th scope="col">ΚΑΤΑΣΚΕΥΑΣΤΗΣ</th>
                                        <th scope="col">ΜΕΡΙΔΑ</th>
                                        <th scope="col">ΠΑΡΑΤΗΡΗΣΕΙΣ</th>
                                        <th scope="col">ΛΕΙΤΟΥΡΓΙΚΟ</th>
                                        <th scope="col">ΧΡΕΩΜΕΝΟ</th>
                                        <th scope="col">S/N</th>
                                        <th scope="col">ΗΜΕΡΟΜΗΝΙΑ ΧΡΕΩΣΗΣ</th>
                                        <th scope="col">ΜΟΝΑΔΑ</th>
                                        <th scope="col">ΤΜΗΜΑ</th>
                                        <th scope="col">ΔΙΚΤΥΟ</th>
                                        <th scope="col">ΗΜΕΡΟΜΗΝΙΑ ΔΙΑΓΡΑΦΗΣ</th>
                                        <th scope="col">ΙΣΤΟΡΙΚΟ</th>
                                        <th scope="col">ΕΠΑΝΑΦΟΡΑ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    try{
                                        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
                                            $monadaid = $_SESSION['id_monada'];
                                            $stmt = mysqli_prepare($dbcon, 'CALL get_deleted_product_info_by_monada(?)');
                                            mysqli_stmt_bind_param($stmt, 'i', $monadaid);
                                            mysqli_stmt_execute($stmt);
                                            mysqli_stmt_store_result($stmt);
                                            $row = [];
                                            mysqli_stmt_bind_result($stmt, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5],
                                                                    $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12], $row[13]);
                                            $aa = 1;
                                            while (mysqli_stmt_fetch($stmt)){
                                                print "<tr>\n";
                                                print "<th scope='row'>$aa</th>\n";
                                                if ($row[6] == 1) $row[6]="Ναι"; else $row[6]="Όχι";
                                                if ($row[7] == 1) $row[7]="Ναι"; else $row[7]="Όχι";
                                                for ($count = 0; $count<14; $count++){
                                                    if($count == 5){
                                                        print "<td class='text-truncate' title='$row[$count]'>$row[$count]</td>\n";
                                                    }else{
                                                        print "<td>$row[$count]</td>\n";
                                                    }                                           
                                                }
                                                print "<td>\n";
                                                print "<button type='button' class='btn btn-primary'
                                                        value='$row[0]' onclick='showHistory(this, this.value, $aa)'>Έλεγχος</button>\n";
                                                print "<div id='txtHint$aa'></div>";
                                                print "</td>\n";
                                                print "<td><button type='button' class='btn btn-danger' data-bs-toggle='modal' 
                                                            data-bs-target='#resurrect_product$row[0]'>Επαναφορά</button></td>\n";
                                                ?>
                                                
                                                <!-- Toggle Form Start (ΓΙΑ ΤΟ BUTTON ΕΠΑΝΑΦΟΡΑΣ)-->
                                                <div class="modal fade" id="resurrect_product<?php print $row[0]; ?>" tabindex="-1" aria-labelledby="resurrect_product<?php print $row[0]; ?>" aria-hidden="true">
                                                    <div class="modal-dialog position-absolute top-50 start-50 translate-middle">
                                                        <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Επαναφορά υλικού</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="post" action="noview_resurrectproduct.php">
                                                        <div class="modal-body"> 
                                                            <div class='text-danger'>Είστε σίγουρος ότι θέλετε να επαναφέρετε το υλικό;</div>
                                                            <input type="hidden"name="barcode" value="<?php print $row[0]; ?>"> 
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-danger">Επαναφορά</button>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                        </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Toggle Form End -->
                                            <?php
                                                print "</tr>\n";
                                                $aa = $aa + 1;
                                            }
                                            mysqli_stmt_close($stmt);
                                        }else{
                                            $monadaid = $_SESSION['id_monada'];
                                            $barcode = filter_input(INPUT_POST, 'barcode', FILTER_VALIDATE_INT);
                                            $typos = filter_input(INPUT_POST, 'typos');
                                            $id_katigoria_prod = filter_input(INPUT_POST, 'id_katigoria_prod', FILTER_VALIDATE_INT);
                                            $id_kataskeuastis = filter_input(INPUT_POST, 'id_kataskeuastis', FILTER_VALIDATE_INT);
                                            $merida = filter_input(INPUT_POST, 'merida');
                                            $leitourgiko = filter_input(INPUT_POST, 'leitourgiko', FILTER_VALIDATE_INT);
                                            $xreomeno = filter_input(INPUT_POST, 'xreomeno', FILTER_VALIDATE_INT);
                                            $id_tmima = filter_input(INPUT_POST, 'id_tmima', FILTER_VALIDATE_INT);
                                            $id_diktyo = filter_input(INPUT_POST, 'id_diktyo', FILTER_VALIDATE_INT);
    
                                            if (empty($barcode)) $barcode="barcode";
                                            if (empty($id_katigoria_prod)) $id_katigoria_prod="id_katigoria_prod";
                                            if (empty($id_kataskeuastis)) $id_kataskeuastis="id_kataskeuastis"; 
                                            if (empty($merida)) $merida="merida";
                                            if (empty($leitourgiko)) $leitourgiko="leitourgiko";
                                            if (empty($xreomeno)) $xreomeno="xreomeno";
                                            if (empty($id_tmima)) $id_tmima="id_tmima";
                                            if (empty($id_diktyo)) $id_diktyo="id_diktyo";
                                            if (empty($typos)) $typos="%"; else $typos = $typos."%";
                                            
                                            $query = "SELECT * FROM deleted_product_info 
                                                    WHERE barcode IN (SELECT barcode FROM products WHERE id_tmima IN (SELECT id_tmima FROM tmima WHERE id_monada=?)
                                                    AND barcode=$barcode AND typos LIKE ? AND id_katigoria_prod=$id_katigoria_prod 
                                                    AND id_kataskeuastis=$id_kataskeuastis AND merida=$merida AND leitourgiko=$leitourgiko 
                                                    AND xreomeno=$xreomeno AND id_tmima=$id_tmima AND id_diktyo=$id_diktyo);";
                                            $stmt = mysqli_prepare($dbcon, $query);
                                            mysqli_stmt_bind_param($stmt, 'is', $monadaid, $typos);  
                                            mysqli_stmt_execute($stmt);
                                            mysqli_stmt_store_result($stmt);
                                            $row = [];
                                            mysqli_stmt_bind_result($stmt, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5],
                                                                    $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12], $row[13]);
                                            $aa = 1;
                                            while (mysqli_stmt_fetch($stmt)){
                                                print "<tr>\n";
                                                print "<th scope='row'>$aa</th>\n";
                                                if ($row[6] == 1) $row[6]="Ναι"; else $row[6]="Όχι";
                                                if ($row[7] == 1) $row[7]="Ναι"; else $row[7]="Όχι";
                                                for ($count = 0; $count<14; $count++){
                                                    if($count == 5){
                                                        print "<td class='text-truncate' title='$row[$count]'>$row[$count]</td>\n";
                                                    }else{
                                                        print "<td>$row[$count]</td>\n";
                                                    }                                           
                                                }
                                                print "<td>\n";
                                                print "<button type='button' class='btn btn-primary'
                                                        value='$row[0]' onclick='showHistory(this, this.value, $aa)'>Έλεγχος</button>\n";
                                                print "<div id='txtHint$aa'></div>";
                                                print "</td>\n";
                                                print "<td><button type='button' class='btn btn-danger' data-bs-toggle='modal' 
                                                            data-bs-target='#resurrect_product$row[0]'>Επαναφορά</button></td>\n";
                                                ?>
                                                
                                                <!-- Toggle Form Start (ΓΙΑ ΤΟ BUTTON ΕΠΑΝΑΦΟΡΑΣ)-->
                                                <div class="modal fade" id="resurrect_product<?php print $row[0]; ?>" tabindex="-1" aria-labelledby="resurrect_product<?php print $row[0]; ?>" aria-hidden="true">
                                                    <div class="modal-dialog position-absolute top-50 start-50 translate-middle">
                                                        <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Επαναφορά υλικού</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="post" action="noview_resurrectproduct.php">
                                                        <div class="modal-body"> 
                                                            <div class='text-danger'>Είστε σίγουρος ότι θέλετε να επαναφέρετε το υλικό;</div>
                                                            <input type="hidden"name="barcode" value="<?php print $row[0]; ?>"> 
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-danger">Επαναφορά</button>
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                        </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Toggle Form End -->
                                            <?php
                                                print "</tr>\n";
                                                $aa = $aa + 1;
                                            }
                                            mysqli_stmt_close($stmt);  
                                        }//if-else (method is get/post)  
                                    }catch (mysqli_sql_exception $e) {
                                        var_dump($e);
                                        $errorpage_url = "errorpage.php?e=" . urlencode($e);
                                        header("Location: $errorpage_url");
                                        exit();
                                    }// try-catch

                                    ?>
                                </tbody>
                            </table>
                            <button onclick="downloadTableAsExcel()" class="btn btn-success">Κατέβασμα όλων σε Excel</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table End -->
<?php
require('../templates/footer.inc.php');
?>