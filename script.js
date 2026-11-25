/* ==========================================================================
   Ember & Oak — shared interactions
   - Mobile nav + sticky header state
   - Scroll-reveal animations (IntersectionObserver, respects reduced motion)
   - Menu data, category tabs, featured dishes on the home page
   - Opening-hours table (highlights today)
   - Reservation form: validates, then POSTs to reserve.php via fetch().
     With no backend (GitHub Pages / file://) it shows a clearly-labelled
     demo success state instead of pretending a booking was made.
   ========================================================================== */
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
      hamburger.setAttribute("aria-label", open ? "Close menu" : "Open menu");
    });
    navLinks.addEventListener("click", function (e) {
      if (e.target.closest("a")) {
        navLinks.classList.remove("open");
        hamburger.classList.remove("open");
        hamburger.setAttribute("aria-expanded", "false");
      }
    });
  }

  /* ---------- Header shadow on scroll ---------- */
  var header = document.getElementById("siteHeader");
  function onScroll() {
    if (header) header.classList.toggle("scrolled", window.scrollY > 10);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* ---------- Scroll reveal ---------- */
  var revealEls = document.querySelectorAll(".reveal");
  if ("IntersectionObserver" in window && revealEls.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add("visible"); });
  }

  /* ---------- Menu data ---------- */
  var MENU = {
    "Starters": [
      { name: "Charred Sourdough & Whipped Ricotta", desc: "Hearth-blistered bread, honey, thyme, smoked salt.", price: 12 },
      { name: "Ember-Roasted Beets", desc: "Whipped goat cheese, pistachio, sherry caramel.", price: 14, tag: "Vegetarian" },
      { name: "Crispy Brussels Sprouts", desc: "Cast-iron seared, miso glaze, sesame, lime.", price: 13, tag: "Vegetarian" },
      { name: "Burrata & Stone Fruit", desc: "Grilled peaches, prosciutto, basil oil, aged balsamic.", price: 16 },
      { name: "Dungeness Crab Toast", desc: "Brown butter, lemon aioli, chives, grilled levain.", price: 18 }
    ],
    "Mains": [
      { name: "Oak-Fired Ribeye", desc: "28-day dry-aged, ember onions, bone-marrow jus. For two.", price: 68, tag: "For two" },
      { name: "Cedar Plank Salmon", desc: "Miso glaze, charred scallions, sesame rice.", price: 34 },
      { name: "Half Chicken al Mattone", desc: "Brick-pressed, rosemary jus, grilled lemon, watercress.", price: 29 },
      { name: "Wild Mushroom Risotto", desc: "Carnaroli rice, parmesan, truffle oil, crispy sage.", price: 26, tag: "Vegetarian" },
      { name: "Smoked Short Rib", desc: "12-hour oak smoke, horseradish cream, pickled shallots.", price: 38 }
    ],
    "Wood-Fired": [
      { name: "Margherita Classica", desc: "San Marzano, fior di latte, basil. 90 seconds in the hearth.", price: 19, tag: "Vegetarian" },
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
      { name: "Blood Orange Spritz", desc: "Non-alcoholic — blood orange, rosemary, soda.", price: 9, tag: "Zero-proof" }
    ]
  };
  var TIME_SLOTS = ["5:00 PM", "5:30 PM", "6:00 PM", "6:30 PM", "7:00 PM", "7:30 PM", "8:00 PM", "8:30 PM", "9:00 PM"];
  var MAX_PARTY = 12;
  var MAX_DAYS_AHEAD = 60;

  function menuItemEl(item) {
    var div = document.createElement("div");
    div.className = "menu-item";
    var head = document.createElement("div");
    head.className = "menu-item-head";
    var h3 = document.createElement("h3");
    h3.textContent = item.name;
    if (item.tag) {
      var tag = document.createElement("span");
      tag.className = "tag";
      tag.textContent = item.tag;
      h3.appendChild(tag);
    }
    var price = document.createElement("span");
    price.className = "menu-price";
    price.textContent = "$" + item.price;
    head.appendChild(h3);
    head.appendChild(price);
    var p = document.createElement("p");
    p.textContent = item.desc;
    div.appendChild(head);
    div.appendChild(p);
    return div;
  }

  /* ---------- Menu tabs (menu.html) ---------- */
  var tabsWrap = document.getElementById("menuTabs");
  var menuList = document.getElementById("menuList");
  var categories = Object.keys(MENU);

  function renderMenu(cat) {
    menuList.innerHTML = "";
    MENU[cat].forEach(function (item) {
      menuList.appendChild(menuItemEl(item));
    });
    menuList.style.animation = "none";
    void menuList.offsetWidth;
    menuList.style.animation = "";
  }

  if (tabsWrap && menuList) {
    categories.forEach(function (cat, i) {
      var btn = document.createElement("button");
      btn.className = "tab" + (i === 0 ? " active" : "");
      btn.textContent = cat;
      btn.type = "button";
      btn.setAttribute("role", "tab");
      btn.setAttribute("aria-selected", i === 0 ? "true" : "false");
      btn.tabIndex = i === 0 ? 0 : -1;
      btn.addEventListener("click", function () {
        tabsWrap.querySelectorAll(".tab").forEach(function (t) {
          t.classList.remove("active");
          t.setAttribute("aria-selected", "false");
          t.tabIndex = -1;
        });
        btn.classList.add("active");
        btn.setAttribute("aria-selected", "true");
        btn.tabIndex = 0;
        renderMenu(cat);
      });
      btn.addEventListener("keydown", function (e) {
        var tabs = Array.prototype.slice.call(tabsWrap.querySelectorAll(".tab"));
        var idx = tabs.indexOf(btn), next = null;
        if (e.key === "ArrowRight") next = tabs[(idx + 1) % tabs.length];
        if (e.key === "ArrowLeft") next = tabs[(idx - 1 + tabs.length) % tabs.length];
        if (e.key === "Home") next = tabs[0];
        if (e.key === "End") next = tabs[tabs.length - 1];
        if (next) { e.preventDefault(); next.focus(); next.click(); }
      });
      tabsWrap.appendChild(btn);
    });
    renderMenu(categories[0]);
  }

  /* ---------- Featured dishes (home page) ---------- */
  var featuredWrap = document.getElementById("featuredDishes");
  if (featuredWrap) {
    var picks = [
      { cat: "Mains", idx: 0, img: "https://picsum.photos/seed/ember-ribeye/800/500" },
      { cat: "Wood-Fired", idx: 0, img: "https://picsum.photos/seed/ember-pizza/800/500" },
      { cat: "Mains", idx: 1, img: "https://picsum.photos/seed/ember-salmon/800/500" }
    ];
    picks.forEach(function (pick, i) {
      var item = MENU[pick.cat][pick.idx];
      var col = document.createElement("div");
      col.className = "col-md-4 reveal" + (i === 1 ? " reveal-delay-1" : i === 2 ? " reveal-delay-2" : "");
      var card = document.createElement("article");
      card.className = "dish-card";
      var img = document.createElement("img");
      img.src = pick.img;
      img.alt = item.name;
      img.loading = "lazy";
      var body = document.createElement("div");
      body.className = "dish-card-body";
      var h3 = document.createElement("h3");
      h3.textContent = item.name;
      var p = document.createElement("p");
      p.textContent = item.desc;
      var price = document.createElement("span");
      price.className = "menu-price";
      price.textContent = "$" + item.price;
      body.appendChild(h3);
      body.appendChild(p);
      body.appendChild(price);
      card.appendChild(img);
      card.appendChild(body);
      col.appendChild(card);
      featuredWrap.appendChild(col);
      if ("IntersectionObserver" in window) io.observe(col);
      else col.classList.add("visible");
    });
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
      var isToday = h.jsDay.indexOf(today) !== -1;
      var tr = document.createElement("tr");
      if (isToday) tr.className = "today";
      var td1 = document.createElement("td");
      td1.textContent = h.days;
      if (isToday) {
        var badge = document.createElement("span");
        badge.className = "today-badge";
        badge.textContent = "Today";
        td1.appendChild(badge);
      }
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
    TIME_SLOTS.forEach(function (t) {
      var o = document.createElement("option");
      o.value = t;
      o.textContent = t;
      timeSelect.appendChild(o);
    });
  }
  if (partySelect) {
    for (var n = 1; n <= MAX_PARTY; n++) {
      var o2 = document.createElement("option");
      o2.value = n;
      o2.textContent = n + (n === 1 ? " guest" : " guests");
      partySelect.appendChild(o2);
    }
  }
  function isoDate(d) {
    return d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") + "-" + String(d.getDate()).padStart(2, "0");
  }
  if (dateInput) {
    dateInput.min = isoDate(new Date());
    var max = new Date();
    max.setDate(max.getDate() + MAX_DAYS_AHEAD);
    dateInput.max = isoDate(max);
  }

  function setError(id, msg) {
    var input = document.getElementById(id);
    var err = document.getElementById("err-" + id);
    if (!input || !err) return false;
    err.textContent = msg || "";
    input.classList.toggle("invalid", !!msg);
    if (msg) input.setAttribute("aria-invalid", "true"); else input.removeAttribute("aria-invalid");
    return !msg;
  }
  function prettyDate(iso) {
    var d = new Date(iso + "T12:00:00");
    return d.toLocaleDateString("en-US", { weekday: "long", month: "long", day: "numeric" });
  }

  var form = document.getElementById("reserveForm");
  var success = document.getElementById("reserveSuccess");
  if (form && success) {
    var submitBtn = document.getElementById("submitBtn");
    var formError = document.getElementById("formError");

    function showSuccess(data, demoMode) {
      var summary = document.getElementById("reserveSummary");
      var partyText = data.party === "1" ? "1 guest" : data.party + " guests";
      summary.textContent = "Table for " + partyText + " on " + prettyDate(data.date) + " at " + data.time + ", under " + data.name + ".";
      document.getElementById("demoBadge").hidden = !demoMode;
      document.getElementById("demoNote").hidden = !demoMode;
      document.getElementById("successTitle").textContent = demoMode ? "Demo booking — looks good!" : "You're booked!";
      document.getElementById("successFine").textContent = demoMode
        ? "This is a front-end demo — nothing was sent or stored."
        : "A confirmation text is on its way. We can't wait to host you.";
      form.hidden = true;
      success.hidden = false;
      success.scrollIntoView({ behavior: "smooth", block: "center" });
    }
    function showFormError(msg) {
      if (!formError) return;
      formError.textContent = msg;
      formError.classList.add("show");
      formError.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      if (formError) formError.classList.remove("show");
      var name = document.getElementById("rName").value.trim();
      var phone = document.getElementById("rPhone").value.trim();
      var date = dateInput.value;
      var time = timeSelect.value;
      var party = partySelect.value;
      var ok = true;
      ok = setError("rName", name.length < 2 ? "Please enter your name." : "") && ok;
      var digits = phone.replace(/\D/g, "");
      ok = setError("rPhone", digits.length < 10 || digits.length > 15 ? "Please enter a valid phone number." : "") && ok;
      if (!date) {
        ok = setError("rDate", "Please choose a date.") && ok;
      } else if (date < dateInput.min) {
        ok = setError("rDate", "Reservations can't be in the past.") && ok;
      } else if (date > dateInput.max) {
        ok = setError("rDate", "Please book within the next 60 days.") && ok;
      } else {
        ok = setError("rDate", "") && ok;
      }
      ok = setError("rTime", !time ? "Please pick a time." : "") && ok;
      ok = setError("rParty", !party ? "Please choose your party size." : "") && ok;
      if (!ok) {
        var firstBad = form.querySelector(".invalid");
        if (firstBad) firstBad.focus();
        return;
      }

      var payload = {
        name: name,
        phone: phone,
        date: date,
        time: time,
        party: party,
        notes: document.getElementById("rNotes").value.trim()
      };

      submitBtn.disabled = true;
      submitBtn.textContent = "Confirming...";

      /* Try the PHP backend. On static hosting (GitHub Pages / file://)
         the fetch fails or returns a non-JSON page — that is expected, and
         we fall back to an honestly-labelled demo state. */
      fetch("reserve.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      })
        .then(function (res) {
          if (!res.ok) throw new Error("HTTP " + res.status);
          var ct = res.headers.get("content-type") || "";
          if (ct.indexOf("application/json") === -1) throw new Error("not json");
          return res.json();
        })
        .then(function (json) {
          if (json && json.ok) {
            showSuccess(payload, false);
          } else if (json && json.errors) {
            showFormError("The restaurant couldn't save that booking: " + json.errors.join(" "));
          } else {
            throw new Error("bad response");
          }
        })
        .catch(function () {
          /* No backend reachable — demo mode, clearly labelled. */
          showSuccess(payload, true);
        })
        .finally(function () {
          submitBtn.disabled = false;
          submitBtn.textContent = "Confirm reservation";
        });
    });

    ["rName", "rPhone", "rDate", "rTime", "rParty"].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) {
        el.addEventListener("input", function () { setError(id, ""); });
        el.addEventListener("change", function () { setError(id, ""); });
      }
    });
    var bookAnother = document.getElementById("bookAnother");
    if (bookAnother) {
      bookAnother.addEventListener("click", function () {
        success.hidden = true;
        form.hidden = false;
        form.reset();
        success.scrollIntoView({ behavior: "smooth", block: "center" });
      });
    }
  }

  /* ---------- Footer year ---------- */
  var yearEl = document.getElementById("year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();
})();


/* === Polish pass 2026-11-25: scroll progress, back-to-top, floating embers, hero parallax === */
(function () {
  "use strict";
  var reduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var bar = document.createElement("div");
  bar.className = "scroll-progress"; bar.setAttribute("aria-hidden", "true");
  document.body.appendChild(bar);
  var topBtn = document.createElement("button");
  topBtn.className = "back-to-top"; topBtn.type = "button";
  topBtn.setAttribute("aria-label", "Back to top"); topBtn.textContent = "\u2191";
  document.body.appendChild(topBtn);
  topBtn.addEventListener("click", function () { window.scrollTo({ top: 0, behavior: reduced ? "auto" : "smooth" }); });
  var hero = document.querySelector(".hero");
  function onScroll() {
    var h = document.documentElement;
    var max = h.scrollHeight - h.clientHeight;
    bar.style.width = (max > 0 ? (h.scrollTop / max) * 100 : 0) + "%";
    topBtn.classList.toggle("show", h.scrollTop > 600);
    if (hero && !reduced) hero.style.backgroundPositionY = "calc(50% + " + Math.min(h.scrollTop * 0.25, 120) + "px)";
  }
  window.addEventListener("scroll", onScroll, { passive: true }); onScroll();
  if (hero && !reduced) {
    for (var i = 0; i < 14; i++) {
      var e = document.createElement("span");
      e.className = "ember"; e.setAttribute("aria-hidden", "true");
      var size = 4 + Math.random() * 7;
      e.style.width = size + "px"; e.style.height = size + "px";
      e.style.left = (Math.random() * 100) + "%";
      e.style.animationDuration = (6 + Math.random() * 7) + "s";
      e.style.animationDelay = (Math.random() * 8) + "s";
      e.style.setProperty("--drift", (Math.random() * 60 - 30) + "px");
      hero.appendChild(e);
    }
  }
})();
