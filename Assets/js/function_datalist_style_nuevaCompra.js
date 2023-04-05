function addActive(x) {
  if (!x) return false
  removeActive(x)
  if (currentFocus >= x.length) currentFocus = 0
  if (currentFocus < 0) currentFocus = x.length - 1
  x[currentFocus].classList.add("active")
}
function removeActive(x) {
  for (var i = 0; i < x.length; i++) {
    x[i].classList.remove("active")
  }
}

/*---------------------------------*/
/*proveedor*/
proveedorId.onfocus = function () {
  proveedorList.style.display = "block"
  proveedorId.style.borderRadius = "5px 5px 0 0"
}
proveedorId.oninput = function () {
  currentFocus2 = -1
  var text = proveedorId.value.toUpperCase()
  for (let option of proveedorList.options) {
    if (option.value.toUpperCase().indexOf(text) > -1) {
      option.style.display = "block"
    } else {
      option.style.display = "none"
    }
  }
}
var currentFocus2 = -1
proveedorId.onkeydown = function (e) {
  if (e.keyCode == 40) {
    currentFocus2++
    addActive(proveedorList.options)
  } else if (e.keyCode == 38) {
    currentFocus2--
    addActive(proveedorList.options)
  } else if (e.keyCode == 13) {
    e.preventDefault()
    if (currentFocus2 > -1) {
      /*and simulate a click on the "active" item:*/
      if (proveedorList.options) proveedorList.options[currentFocus2].click()
    }
  }
}
/*---------------------------------*/

/*---------------------------------*/
/*sucursal*/
sucursalId.onfocus = function () {
  sucursalList.style.display = "block"
  sucursalId.style.borderRadius = "5px 5px 0 0"
}
sucursalId.oninput = function () {
  currentFocus3 = -1
  var text = sucursalId.value.toUpperCase()
  for (let option of sucursalList.options) {
    if (option.value.toUpperCase().indexOf(text) > -1) {
      option.style.display = "block"
    } else {
      option.style.display = "none"
    }
  }
}
var currentFocus3 = -1
sucursalId.onkeydown = function (e) {
  if (e.keyCode == 40) {
    currentFocus3++
    addActive(sucursalList.options)
  } else if (e.keyCode == 38) {
    currentFocus3--
    addActive(sucursalList.options)
  } else if (e.keyCode == 13) {
    e.preventDefault()
    if (currentFocus3 > -1) {
      /*and simulate a click on the "active" item:*/
      if (sucursalList.options) sucursalList.options[currentFocus3].click()
    }
  }
}
/*---------------------------------*/