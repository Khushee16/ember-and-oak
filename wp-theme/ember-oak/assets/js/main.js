/* Ember & Oak theme — mobile nav, header state, scroll reveals,
   menu category tabs, opening-hours table. */
(function () {
  "use strict";

  /* Mobile nav */
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

  /* Header shadow on scroll */
  var header = document.getElementById("siteHeader");
  function onScroll() {
    if (header) header.classList.toggle("scrolled", window.scrollY > 10);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* Scroll reveal */
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

  /* Menu category tabs (Menu page template) */
  var tabsWrap = document.getElementById("menuTabs");
  if (tabsWrap) {
    var tabs = tabsWrap.querySelectorAll(".tab");
    tabs.forEach(function (tab) {
      tab.addEventListener("click", function () {
        var cat = tab.getAttribute("data-category");
        tabs.forEach(function (t) {
          t.classList.remove("active");
          t.setAttribute("aria-selected", "false");
        });
        tab.classList.add("active");
        tab.setAttribute("aria-selected", "true");
        document.querySelectorAll(".menu-category").forEach(function (list) {
          var show = list.getAttribute("data-category") === cat;
          list.hidden = !show;
          if (show) {
            list.style.animation = "none";
            void list.offsetWidth;
            list.style.animation = "";
          }
        });
      });
    });
  }

  /* Opening hours (Reservations page template) */
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

  /* Reservation date bounds (today .. +60 days) */
  var dateInput = document.getElementById("rDate");
  if (dateInput) {
    var iso = function (d) {
      return d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") + "-" + String(d.getDate()).padStart(2, "0");
    };
    dateInput.min = iso(new Date());
    var max = new Date();
    max.setDate(max.getDate() + 60);
    dateInput.max = iso(max);
  }
})();
