//charts v4
const ctx = document.getElementById("myChart")
const ctx2 = document.getElementById("myChart2")

const labels1 = []
const data1 = []
const colores = [
  "rgb(255, 99, 132)",
  "rgb(255, 159, 64)",
  "rgb(255, 205, 86)",
  "rgb(75, 192, 192)",
  "rgb(54, 162, 235)",
  "rgb(153, 102, 255)",
  "rgb(201, 203, 207)",
  "rgb(255, 99, 132)",
  "rgb(255, 159, 64)",
  "rgb(255, 205, 86)",
  "rgb(75, 192, 192)",
  "rgb(54, 162, 235)",
  "rgb(153, 102, 255)",
  "rgb(201, 203, 207)",
  "rgb(255, 99, 132)",
  "rgb(255, 159, 64)",
  "rgb(255, 205, 86)",
  "rgb(75, 192, 192)",
  "rgb(54, 162, 235)",
  "rgb(153, 102, 255)"
]
const labels2 = []
const data2 = []

$(document).ready(function () {
  $.ajax({
    type: "GET",
    url: base_url + "Dashboard/productosVentaCantidad",
    dataType: "json",
    success: function (response) {
      // console.log(response)
      $("#PVCantidad").html(response.cantidad)
    },
  })

  $.ajax({
    type: "GET",
    url: base_url + "Dashboard/productoMasVendido",
    dataType: "json",
    success: function (response) {
      // console.log(response)
      $("#PVMasPedido").html(response.NOMBRE)
    },
  })

  $.ajax({
    type: "GET",
    url: base_url + "Dashboard/ingresosDelDia",
    dataType: "json",
    success: function (response) {
      // console.log(response)
      $("#PVIngresosDia").html(response.total)
    },
  })

  $.ajax({
    type: "GET",
    url: base_url + "Dashboard/totalProductosVendidos",
    dataType: "json",
    success: function (response) {
      // console.log(response)
      $("#PVTotalVenta").html(parseInt(response.total_ventas))
    },
  })

  $.ajax({
    type: "GET",
    url: base_url + "Dashboard/grafico1",
    dataType: "json",
    success: function (response) {
      // console.log(response)
      response.forEach((element) => {
        // console.log(element)
        labels1.push(element.NOMBRE)
        data1.push(element.cantidades)
      })
      console.log(labels1)
      console.log(data1)
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels1,
          datasets: [
            {
              label: "Productos (cantidad)",
              data: data1,
              borderWidth: 1,
            },
          ],
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      })
    },
  })

  $.ajax({
    type: "GET",
    url: base_url + "Dashboard/grafico2",
    dataType: "json",
    success: function (response) {
      // console.log(response)
      response.forEach((element) => {
        // console.log(element)
        labels2.push(element.NOMBRE)
        data2.push(element.totales)
      })
      console.log(labels2)
      console.log(data2)
      new Chart(ctx2, {
        type: "pie",
        data: {
          labels: labels2,
          datasets: [
            {
              label: "Dataset 1",
              data: data2,
              backgroundColor: colores,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "right",
            },
            title: {
              display: true,
              text: "Monto en $ (pesos)",
            },
          },
        },
      })
    },
  })
})
