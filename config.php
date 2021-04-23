<?php
$file = file_get_contents('mots.txt');
$mots=explode(',', $file);
sort($mots);
$res="";
if(isset($_POST['suppr']) && $_POST['suppr']!=""){
    $mots = array_diff($mots, [$_POST['suppr']]);
    save(implode(',', $mots));
    $res="Suppresion de ".$_POST['suppr']." effectué";
}else if(isset($_POST['mot']) && $_POST['mot']!=""){
    if(!in_array($_POST['mot'], $mots)) {
        save(implode(',', $mots) . "," . $_POST['mot']);
        $mots[]= $_POST['mot'];
        $res="Ajout de ".$_POST['mot']." effectué";
    }
    else{
        $res=$_POST['mot']." déjà présent";
    }
    $_POST['mot']=null;
}
function save($txt){
    $file = fopen('mots.txt', 'w');
    fwrite($file, $txt);
    fclose($file);
    $file = file_get_contents('mots.txt');
    $mots = explode(',', $file);
}
?>

<html>
<style>
    .container{
        margin-top: 25px;
    }

    td{
        margin: 5px;
    }
    table{
        width: 100%;
    }
    input{
        width: 100px;
    }

</style>
<div>
    <head>
        <link rel="stylesheet" href="http_bootswatch.com_4_flatly_bootstrap.css">
        <title>Liste des mots a bannir</title>
    </head>
    <body>
    <div class="container">
        <h5><?php echo $res?></h5>
        <form method="post" id="formConfig">
            <input type="hidden" id="suppr" name="suppr">
        <h1>Ajouter un mot:</h1>
        <div class="form-group">
            <input type="text" required class="form-control" name="mot" id="mot">
        </div>
        <div class="form-group">
            <input type="submit" class="form-control btn btn-info"  value="Ajouter">
        </div>
        <h1>Mots:</h1>
        <table>
        <?php
        $i=0;
        foreach ($mots as $key=>$m){
            if($i%5==0) {
                if ($i != 0)
                    echo "</tr>";
                echo "<tr>";
            }

        ?>
        <td id="<?php echo $key?>">
                <input type="text" readonly id="inp<?php echo $key?>" name="<?php echo $key?>" value="<?php echo $m?>">
                <button type="button" class="btn-sm btn-danger" onclick="Suppr(<?php echo $key?>)">Supprimer</button>
        </td>
        <?php
            $i++;
        }
        ?>
        </table>
        </form>
    </div>
    </body>


</div>
<script>
    function Suppr(m){
        document.getElementById('suppr').value =document.getElementById('inp'+m).value;
        document.getElementById(m).remove();
        document.getElementById('formConfig').submit();

    }
</script>

</html>

