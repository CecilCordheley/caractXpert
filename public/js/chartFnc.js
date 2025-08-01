HTMLCanvasElement.prototype.clearCanvas=function(){
    this.clearRect(0,0,this.width,this.height);
}
function destroyChartInstance(ctx) {
  Object.values(Chart.instances).forEach(instance => {
    if (instance.ctx === ctx) {
      instance.destroy();
    }
  });
}

async function countByPanne(){
  getStat("countByPanne", (data) => {
     let panneChartInstance = null;
      let canvas=document.getElementById("panneChart");
      canvas.innerHTML="";
      const ctx = canvas.getContext('2d');
 const labels = [];
      const counts = [];

      Object.keys(data).forEach(key => {
        const entry = data[key];
        labels.push(entry.panne.code);      // ou .diagnostique si tu veux plus de détails
        counts.push(entry.nb);
      });
      if (panneChartInstance) {
        panneChartInstance.destroy();
      }
      destroyChartInstance(ctx);
     panneChartInstance= new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Nombre de pannes',
            data: counts,
            backgroundColor: [
              '#ff6384',
              '#36a2eb',
              '#ffce56'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false },
            title: { display: true, text: 'Répartition des pannes détectées' }
          },
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Nombre'
              }
            }
          }
        }
      })
      console.dir(data);
    }, (err) => {
      console.dir(err);
    })
}