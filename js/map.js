var map = L.map('map').setView([45.68,0.19],12);
L.tileLayer('https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=uZySEUrEohpoPk5ayniQ', {
  attribution:'<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',

}).addTo(map);

window.addEventListener("load", function(){
    map.flyTo([45.68,0.19],12);
});

function isMobileDevice() {
    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
};

// window.navigator.geolocation.getCurrentPosition(function(position) {
//   if (isMobileDevice()) {
//     var x = position.coords.longitude;
//     var y = position.coords.latitude;
//     console.log(x,y);
//     L.marker([x, y]).addTo(map)
//       .bindPopup('Votre position')
//       .openPopup();
//   }
//
// }, function(error) {
//   console.log("Erreur de géoloc N°"+error.code+" : "+error.message);
//   console.log(error);
// });

// var popup = L.popup({
//   closeButton:false,
//   autoClose:false,
//   closeOnEscapeKey:false,
//   closeOnClick:false,
//   minWidth:100
// })  .setLatLng(L.latLng(45.711718,0.145872))
//     .setContent('<a style="display:inline-flex;text-align:center"><i class="fas fa-utensils"></i>&nbsp;&nbsp;Le Guez\'t</a>')
//     .openOn(map);

window.addEventListener('load', function () {
  // Get the modal
  var modal = document.getElementById("addResto");

  // Get the button that opens the modal
  var btn = document.getElementById("addButton");

  // Get the <span> element that closes the modal
  var span = document.getElementById("close");

  // When the user clicks on the button, open the modal
  btn.onclick = function() {
    modal.style.display = "block";
  }

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
  }

  var submit = document.getElementById("submit-button");
  var nom = document.getElementById("nom");
  var commune = document.getElementById("commune");
  var plat = document.getElementById("plat");
  var x = document.getElementById("x");
  var y = document.getElementById("y");
  var form = document.getElementById("form-resto");

  submit.onclick = function() {
    var check=true;
    if (nom.value=="") {
      nom.className="input is-danger";
      check=false;
    }else{
      nom.className="input is-success";
    }

    if (!parseFloat(x.value)) {
      x.className="input is-danger";
      check=false;
    }else{
      x.className="input is-success";
    }

    if (!parseFloat(y.value)) {
      y.className="input is-danger";
      check=false;
    }else{
      y.className="input is-success";
    }

    if (commune.value=="") {
      commune.className="input is-warning";
    }else{
      commune.className="input is-success";
    }

    if (plat.value=="") {
      plat.className="input is-warning";
    }else if (parseFloat(plat.value)){
      plat.className="input is-success";
    }else{
      plat.className="input is-danger";
      check=false;
    }

    if (check==true) {
      form.setAttribute("onSubmit", "");
      form.submit();
    }
  }

  class StarRating extends HTMLElement {
      get value () {
          return this.getAttribute("value") || 0;
      }

      set value (val) {
          this.setAttribute("value", val);
          this.highlight(this.value - 1);
          var inputs = document.getElementsByClassName('star-input');
          var i;
          for (i = 0; i < inputs.length; ++i) {
              inputs[i].setAttribute("value", val);
          }
      }

      get number () {
          return this.getAttribute("number") || 5;
      }

      set number (val) {
          this.setAttribute("number", val);
          var inputs = document.getElementsByClassName('star-input');
          var i;
          for (i = 0; i < inputs.length; ++i) {
              inputs[i].setAttribute("value", val);
          }

          this.stars = [];

          while (this.firstChild) {
              this.removeChild(this.firstChild);
          }

          for (let i = 0; i < this.number; i++) {
              let s = document.createElement("div");
              s.className = "star";
              this.appendChild(s);
              this.stars.push(s);
          }

          this.value = this.value;
      }

      highlight (index) {
          this.stars.forEach((star, i) => {
              star.classList.toggle("fas", i <= index);
          });
      }

      constructor () {
          super();

          this.number = this.number;

          document.querySelectorAll(".star").forEach(item => {
            item.addEventListener("mousemove", e => {
              let box = item.getBoundingClientRect(),
                  starIndex = Math.floor((e.pageX - box.left) / box.width * item.stars.length);

              item.highlight(starIndex);
            })
            item.addEventListener("mouseout", e => {
              item.value = item.value;
            })
            item.addEventListener("click", e => {
              let box = item.getBoundingClientRect(),
                  starIndex = Math.floor((e.pageX - box.left) / box.width * item.stars.length);

              item.value = starIndex + 1;

              let rateEvent = new Event("rate");
              item.dispatchEvent(rateEvent);
            });
          })

      }
  }

  customElements.define("x-star-rating", StarRating);
})
