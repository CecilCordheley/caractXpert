<div class="row">
    <div class="col-6 offset-2">
        <div class="mb-3">
            <label for="Mail_Connexion" class="form-label">Mail</label>
            <input type="mail" id="Mail_Connexion" class="form-control">
        </div>
        <div class="mb-3">
            <label for="Mdp_Connexion" class="form-label">Mot de passe</label>
            <input type="password" id="Mdp_Connexion" class="form-control">
        </div>
        <div class="mb-3">
            <label for="checkPassWord" class="form-label">Répétez le mot de passe</label>
            <input type="password" id="checkPassWord" class="form-control">
        </div>
        <button class="btn btn-primary" id="generateTrigger">Créer mon mot de passe</button>
    </div>
</div>
<script>
    document.getElementById("generateTrigger").onclick=function(){
        let mail=document.getElementById("Mail_Connexion").value;
        let mdp=document.getElementById("Mdp_Connexion").value;
        createPassWord(mail,mdp,()=>{
            _alert("Votre mot de passe a été créé",function(){
                window.location.href="index.php";
            })
        },(err)=>{
            _alert("Une erreur s'est produite : <b>"+err+"</b>",1);
        })
    }
</script>