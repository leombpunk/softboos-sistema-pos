//cliente hace referencia al id de la etiqueta del input asociado al datalist
//el input debe tener el atributo 'list' vacio
//clientList hace referencia al id de la etiqueta del datalist
cliente.onfocus = function () {
  clientList.style.display = "block"
  cliente.style.borderRadius = "5px 5px 0 0"
}
for (let option of clientList.options) {
  option.onclick = function () {
    cliente.value = option.value
    clientList.style.display = "none"
    cliente.style.borderRadius = "5px"
  }
}

cliente.oninput = function () {
  currentFocus = -1
  var text = cliente.value.toUpperCase()
  for (let option of clientList.options) {
    if (option.value.toUpperCase().indexOf(text) > -1) {
      option.style.display = "block"
    } else {
      option.style.display = "none"
    }
  }
}
var currentFocus = -1
cliente.onkeydown = function (e) {
  if (e.keyCode == 40) {
    currentFocus++
    addActive(clientList.options)
  } else if (e.keyCode == 38) {
    currentFocus--
    addActive(clientList.options)
  } else if (e.keyCode == 13) {
    e.preventDefault()
    if (currentFocus > -1) {
      /*and simulate a click on the "active" item:*/
      if (clientList.options) clientList.options[currentFocus].click()
    }
  }
}

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
