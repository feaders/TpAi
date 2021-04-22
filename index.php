<?php
if(isset($_POST['lien'])) {
    $res = shell_exec('python resume.py '.$_POST['lien']);
    $resDecode=json_decode(utf8_encode($res));
    if($resDecode == null){
        $_POST['freq']="Le lien n'est pas comatible";
        $_POST['resume'] ="";

    }
    else {
        $txt = str_replace('', "-", $resDecode->resume);
        $txt = str_replace('', "'", $txt);
        $txt = str_replace('', "oe", $txt);

        $_POST['resume'] =$txt;
        $_POST['freq'] = $resDecode->freq;
    }

}

?>
<html>
<style>
    .container{
        margin-top: 25px;
    }
    #resume{
        min-height: 300px;
    }

</style>
<div>
    <head>
        <link rel="stylesheet" href="https://bootswatch.com/4/flatly/bootstrap.min.css">
        <title>Resumé facile</title>
    </head>
    <body>
    <div class="container">
        <h1>Resumé un article en 2 clics !!</h1>
        <form method="post">
            <div class="form-group">
                <label for="lien">Lien</label>
                <input type="text" required class="form-control" name="lien" id="lien" value="<?php if(isset($_POST['lien'])) echo$_POST['lien'];?>">
            </div>
            <div class="form-group">
                <input type="submit" class="form-control btn btn-info"  value="Valider">
            </div>
            <div class="form-group">
                <label for="freq">10 mots les plus fréquents</label>
                <input type="text" readonly class="form-control" name="freq" id="freq" value="<?php if(isset($_POST['freq'])) echo$_POST['freq'];?>">
            </div>
            <div class="form-group">
                <label for="resume">Resume</label>
                <textarea  readonly class="form-control" name="resume" id="resume"><?php if(isset($_POST['resume'])) echo substr($_POST['resume'],1);?></textarea>
            </div>
    </div>
    </form>
    </body>


</div>

</html>
