/* ============ Ember & Oak interactions ============ */
(function () {
  "use strict";

  /* ---------- Mobile nav ---------- */
  var hamburger = document.getElementById("hamburger");
  var navLinks = document.getElementById("navLinks");
  if (hamburger && navLinks) {
    hamburger.addEventListener("click", function () {
      var open = navLinks.classList.toggle("open");
      hamburger.classList.toggle("open", open);
      hamburger.setAttribute("aria-expanded", open ? "true" : "false");
    });
    navLinks.addEventListener("click", function (e) {
      if (e.target.closest("a")) {
        navLinks.classList.remove("open");
        hamburger.classList.remove("open");
        hamburger.setAttribute("aria-expanded", "false");
      }
    });
  }

  /* ---------- Menu data + tabs ---------- */
  var MENU = {
    "Starters": [
      { name: "Charred Sourdough & Whipped Ricotta", desc: "Hearth-blistered bread, honey, thyme, smoked salt.", price: 12 },
      { name: "Ember-Roasted Beets", desc: "Whipped goat cheese, pistachio, sherry caramel.", price: 14 },
      { name: "Crispy Brussels Sprouts", desc: "Cast-iron seared, miso glaze, sesame, lime.", price: 13 },
      { name: "Burrata & Stone Fruit", desc: "Grilled peaches, prosciutto, basil oil, aged balsamic.", price: 16 },
      { name: "Dungeness Crab Toast", desc: "Brown butter, lemon aioli, chives, grilled levain.", price: 18 }
    ],
    "Mains": [
      { name: "Oak-Fired Ribeye", desc: "28-day dry-aged, ember onions, bone-marrow jus. For two.", price: 68 },
      { name: "Cedar Plank Salmon", desc: "Miso glaze, charred scallions, sesame rice.", price: 34 },
      { name: "Half Chicken al Mattone", desc: "Brick-pressed, rosemary jus, grilled lemon, watercress.", price: 29 },
      { name: "Wild Mushroom Risotto", desc: "Carnaroli rice, parmesan, truffle oil, crispy sage.", price: 26 },
      { name: "Smoked Short Rib", desc: "12-hour oak smoke, horseradish cream, pickled shallots.", price: 38 }
    ],
    "Wood-Fired": [
      { name: "Margherita Classica", desc: "San Marzano, fior di latte, basil. 90 seconds in the hearth.", price: 19 },
      { name: "Soppressata & Hot Honey", desc: "Calabrian chili, mozzarella, hot honey drizzle.", price: 22 },
      { name: "Flatbread of the Day", desc: "Ask your server — built around the morning market run.", price: 18 },
      { name: "Whole Branzino", desc: "Fire-roasted whole fish, salsa verde, grilled lemon.", price: 36 }
    ],
    "Desserts": [
      { name: "Burnt Basque Cheesecake", desc: "Caramelized top, macerated plums.", price: 12 },
      { name: "Chocolate Budino", desc: "Olive oil, sea salt, hearth-toasted hazelnuts.", price: 11 },
      { name: "Wood-Oven Apple Crisp", desc: "Brown butter oat crumble, vanilla gelato.", price: 12 },
      { name: "Affogato", desc: "Vanilla gelato drowned in double espresso.", price: 8 }
    ],
    "Drinks": [
      { name: "Smoked Old Fashioned", desc: "Oak-smoked bourbon, demerara, black walnut bitters.", price: 15 },
      { name: "Fig Leaf Martini", desc: "Fig-leaf gin, dry vermouth, orange oils.", price: 14 },
      { name: "Natural Red Flight", desc: "Three low-intervention reds, sommelier's pick.", price: 22 },
      { name: "Blood Orange Spritz", desc: "Non-alcoholic — blood orange, rosemary, soda.", price: 9 }
    ]
  };

  var tabsWrap = document.getElementById("menuTabs");
  var menuList = document.getElementById("menuList");
  var categories = Object.keys(MENU);

  function renderMenu(cat) {
    menuList.innerHTML = "";
    MENU[cat].forEach(function (item) {
      var div = document.createElement("div");
      div.className = "menu-item";
      var head = document.createElement("div");
      head.className = "menu-item-head";
      var h3 = document.createElement("h3");
      h3.textContent = item.name;
      var price = document.createElement("span");
      price.className = "menu-price";
      price.textContent = "$" + item.price;
      head.appendChild(h3);
      head.appendChild(price);
      var p = document.createElement("p");
      p.textContent = item.desc;
      div.appendChild(head);
      div.appendChild(p);
      menuList.appendChild(div);
    });
    // retrigger entrance animation
    menuList.style.animation = "none";
    void menuList.offsetWidth;
    menuList.style.animation = "";
  }

  if (tabsWrap && menuList) {
    categories.forEach(function (cat, i) {
      var btn = document.createElement("button");
      btn.className = "tab" + (i === 0 ? " active" : "");
      btn.textContent = cat;
      btn.setAttribute("role", "tab");
      btn.setAttribute("aria-selected", i === 0 ? "true" : "false");
      btn.addEventListener("click", function () {
        var tabs = tabsWrap.querySelectorAll(".tab");
        tabs.forEach(function (t) {
          t.classList.remove("active");
          t.setAttribute("aria-selected", "false");
        });
        btn.classList.add("active");
        btn.setAttribute("aria-selected", "true");
        renderMenu(cat);
      });
      tabsWrap.appendChild(btn);
    });
    renderMenu(categories[0]);
  }

  /* ---------- Opening hours ---------- */
  var HOURS = [
    { days: "Monday – Thursday", time: "5:00 PM – 10:00 PM", jsDay: [1, 2, 3, 4] },
    { days: "Friday – Saturday", time: "5:00 PM – 11:00 PM", jsDay: [5, 6] },
    { days: "Sunday", time: "4:00 PM – 9:00 PM", jsDay: [0] }
  ];
  var hoursBody = document.querySelector("#hoursTable tbody");
  if (hoursBody) {
    var today = new Date().getDay();
    HOURS.forEach(function (h) {
      var tr = document.createElement("tr");
      if (h.jsDay.indexOf(today) !== -1) tr.className = "today";
      var td1 = document.createElement("td");
      td1.textContent = h.days + (h.jsDay.indexOf(today) !== -1 ? " · Today" : "");
      var td2 = document.createElement("td");
      td2.textContent = h.time;
      tr.appendChild(td1);
      tr.appendChild(td2);
      hoursBody.appendChild(tr);
    });
  }

  /* ---------- Reservation form ---------- */
  var timeSelect = document.getElementById("rTime");
  var partySelect = document.getElementById("rParty");
  var dateInput = document.getElementById("rDate");

  if (timeSelect) {
    ["5:00 PM", "5:30 PM", "6:00 PM", "6:30 PM", "7:00 PM", "7:30 PM", "8:00 PM", "8:30 PM", "9:00 PM"].forEach(function (t) {
      var o = document.createElement("option");
      o.value = t;
      o.textContent = t;
      timeSelect.appendChild(o);
    });
  }
  if (partySelect) {
    for (var n = 1; n <= 12; n++) {
      var o2 = document.createElement("option");
      o2.value = n;
      o2.textContent = n + (n === 1 ? " guest" : " guests");
      partySelect.appendChild(o2);
    }
  }
  if (dateInput) {
    var todayStr = new Date().toISOString().split("T")[0];
    dateInput.min = todayStr;
    var max = new Date();
    max.setDate(max.getDate() + 60);
    dateInput.max = max.toISOString().split("T")[0];
  }

  function setError(id, msg) {
    var input = document.getElementById(id);
    var err = document.getElementById("err-" + id);
    if (!input || !err) return false;
    err.textContent = msg || "";
    input.classList.toggle("invalid", !!msg);
    return !msg;
  }
  function prettyDate(iso) {
    var d = new Date(iso + "T12:00:00");
    return d.toLocaleDateString("en-US", { weekday: "long", month: "long", day: "numeric" });
  }

  var form = document.getElementById("reserveForm");
  var success = document.getElementById("reserveSuccess");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var name = document.getElementById("rName").value.trim();
      var phone = document.getElementById("rPhone").value.trim();
      var date = dateInput.value;
      var time = timeSelect.value;
      var party = partySelect.value;
      var ok = true;
      ok = setError("rName", name.length < 2 ? "Please enter your name." : "") && ok;
      var digits = phone.replace(/\D/g, "");
      ok = setError("rPhone", digits.length < 10 ? "Please enter a valid 10-digit phone number." : "") && ok;
      if (!date) {
        ok = setError("rDate", "Please choose a date.") && ok;
      } else if (date < dateInput.min) {
        ok = setError("rDate", "Reservations can't be in the past.") && ok;
      } else {
        ok = setError("rDate", "") && ok;
      }
      ok = setError("rTime", !time ? "Please pick a time." : "") && ok;
      ok = setError("rParty", !party ? "Please choose your party size." : "") && ok;
      if (!ok) return;

      var summary = document.getElementById("reserveSummary");
      var partyText = party === "1" ? "1 guest" : party + " guests";
      summary.textContent = "Table for " + partyText + " on " + prettyDate(date) + " at " + time + ", under " + name + ".";
      form.hidden = true;
      success.hidden = false;
      success.scrollIntoView({ behavior: "smooth", block: "center" });
    });
    ["rName", "rPhone", "rDate", "rTime", "rParty"].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.addEventListener("input", function () { setError(id, ""); });
      if (el) el.addEventListener("change", function () { setError(id, ""); });
    });
    var bookAnother = document.getElementById("bookAnother");
    if (bookAnother) {
      bookAnother.addEventListener("click", function () {
        success.hidden = true;
        form.hidden = false;
        form.reset();
      });
    }
  }

  /* ---------- Footer year ---------- */
  var yearEl = document.getElementById("year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();
})();
