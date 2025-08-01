<div class="row">
  <div id="sysContent" class="col-4">
    <h2>Bienvenue {var:user.nomUser} {var:user.prenomUser} </h2>
    <canvas id="panneChart" height="200"></canvas>
  </div>
  <div class="col-8" id="MainActivity">
    Ici les composant d'activité
  </div>
  <script>
    getMainActivity();
    countByPanne();
  </script>
</div>