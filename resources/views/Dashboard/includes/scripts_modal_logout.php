<script>
  function openLogoutModal() {
    var logoutModal = document.getElementsByClassName("logout-modal");
    logoutModal[0].setAttribute('class', 'modal logout-modal open-modal');

    var logoutModalContent = document.getElementsByClassName("logout-modal-content");
    logoutModalContent[0].setAttribute('class', 'modal-content logout-modal-content open-modal-content');

    document.body.setAttribute('class','body-fixed');
  }

  function closeLogoutModal() {
    var logoutModal = document.getElementsByClassName("logout-modal");
    logoutModal[0].setAttribute('class', 'modal logout-modal');

    var logoutModalContent = document.getElementsByClassName("logout-modal-content");
    logoutModalContent[0].setAttribute('class', 'modal-content logout-modal-content');

    document.body.removeAttribute('class');
  }
</script>