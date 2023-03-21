//Que me interesa ver en el dashboard (resumenes de que informacion del negocio)?
//Producto mas vendido (cantidad por dia, semana, mes)
//Producto que mas dinero deja (dinero por día, semana, mes -> creo que al mes es lo mejor)
//Top (5) de mejores productos, tanto en cantidad como en ganancia (puede usarse en un grafico, o sea 2 graficos)
//Cantidad de productos a la venta (para tener como referencia y hacer comparaciones con el top 5 -> puede ser un top mas grande)
//En el caso que existan varios clientes se puede hacer un top de clientes
//total de productos vendidos hasta la fecha
$(document).ready(function() {
  $.ajax({
    type: "GET",
    url: base_url+"Dashboard/productosVentaCantidad",
    dataType: "json",
    success: function(response) {
      console.log(response);
      $("#PVCantidad").html(response.cantidad);
    }
  });
  $.ajax({
    type: "GET",
    url: base_url+"Dashboard/productoMasVendido",
    dataType: "json",
    success: function(response) {
      console.log(response);
      $("#PVMasPedido").html(response.NOMBRE);
    }
  });
  $.ajax({
    type: "GET",
    url: base_url+"Dashboard/ingresosDelDia",
    dataType: "json",
    success: function(response) {
      console.log(response);
      $("#PVIngresosDia").html(response.total);
    }
  });
  $.ajax({
    type: "GET",
    url: base_url+"Dashboard/totalProductosVendidos",
    dataType: "json",
    success: function(response) {
      console.log(response);
      $("#PVTotalVenta").html(parseInt(response.total_ventas));
    }
  });
})
var data = {
    labels: ["January", "February", "March", "April", "May"],
    datasets: [
        {
            label: "me chupa tres huevos",
            fillColor: "rgba(151,0,205,0.2)",
            strokeColor: "rgba(151,0,205,1)",
            pointColor: "rgba(151,0,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,0,205,1)",
            data: [15, 100, 55, 68, 10]
        },
        {
            label: "My First dataset",
            fillColor: "rgba(220,220,220,0.2)",
            strokeColor: "rgba(220,220,220,1)",
            pointColor: "rgba(220,220,220,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(220,220,220,1)",
            data: [65, 59, 80, 81, 56]
        },
        {
            label: "My Second dataset",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [28, 48, 40, 19, 86]
        }
        
    ]
};
var pdata = [
    {
        value: 300,
        color: "#46BFBD",
        highlight: "#5AD3D1",
        label: "Complete"
    },
    {
        value: 50,
        color:"#F7464A",
        highlight: "#FF5A5E",
        label: "In-Progress"
    },
    {
        value: 100,
        color: "#a55622",
        highlight: "#FF8741",
        label: "test1"
    },
    {
        value: 500,
        color: "#106bc3",
        highlight: "#225b93",
        label: "test2"
    },
    {
        value: 55,
        color: "#299147",
        highlight: "#39cb63",
        label: "test3"
    }
]
const bdata = {
    labels: ["Producto 1", "Producto 2", "Producto 3", "Producto 4", "Producto 5"],
    datasets: [
        // {
        //     label: "My First dataset",
        //     fillColor: "rgba(220,220,220,0.5)",
        //     strokeColor: "rgba(220,220,220,0.8)",
        //     highlightFill: "rgba(220,220,220,0.75)",
        //     highlightStroke: "rgba(220,220,220,1)",
        //     data: [65, 59, 80, 81, 56]
        // },
        {
            label: "My Second dataset",
            fillColor: "rgba(151,187,205,0.5)",
            strokeColor: "rgba(151,187,205,0.8)",
            highlightFill: "rgba(151,187,205,0.75)",
            highlightStroke: "rgba(151,187,205,1)",
            data: [28, 48, 40, 19, 86]
        }
    ]
};

const ddata = [
    {
        value: 300,
        color:"#F7464A",
        highlight: "#FF5A5E",
        label: "Producto 1\ncantidad:"
    },
    {
        value: 50,
        color: "#46BFBD",
        highlight: "#5AD3D1",
        label: "Producto 2"
    },
    {
        value: 100,
        color: "#FDB45C",
        highlight: "#FFC870",
        label: "Producto 3"
    }
];

// var ctxl = $("#lineChartDemo").get(0).getContext("2d");
// var lineChart = new Chart(ctxl).Line(data);

// var ctxp = $("#pieChartDemo").get(0).getContext("2d");
// var pieChart = new Chart(ctxp).Pie(pdata);

// var ctxb = $("#barChartDemo").get(0).getContext("2d");
// var barChart = new Chart(ctxb).Bar(bdata);

// var ctxd = $("#doughnutChartDemo").get(0).getContext("2d");
// var doughnutChart = new Chart(ctxd).Doughnut(ddata);


//chart v4
const ctx = document.getElementById('myChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
    datasets: [{
      label: '# of Votes',
      data: [12, 19, 3, 5, 2, 3],
      borderWidth: 1
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});


const ctx2 = document.getElementById('myChart2');
new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: ['Red', 'Orange', 'Yellow', 'Green', 'Blue'],
        datasets: [
          {
            label: 'Dataset 1',
            data: [12, 19, 3, 5, 2],
            backgroundColor: ['rgb(255, 99, 132)','rgb(255, 159, 64)','rgb(255, 205, 86)','rgb(75, 192, 192)','rgb(54, 162, 235)'],
            
          }
        ]
      },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'right',
        },
        title: {
          display: true,
          text: 'Chart.js Pie Chart'
        }
      }
    },
});