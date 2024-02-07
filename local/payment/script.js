window.addEventListener("load", () => {
  const card = document.querySelector(".card");
  const crossPay = document.querySelector(".cross-pay");
  const brandContent = document.getElementById("brandContent");
  const baseUrl = window.location.origin + "/lms";
  const userid = document.getElementById("userid");
  const courseid = document.getElementById("courseid");
  const couponid = document.querySelector(".couponid");
  const bought = $("#alreadyPaid").val();
  const amount = document.getElementById("amount");
  const pay_username = document.getElementById("pay_username");
  let callback_url = document.getElementById("callback_url");
  const btnPayment = document.getElementById("btn-payment");
  const new_registration_btn = document.querySelector(".btn1");
  const login1 = document.querySelector(".login1");
  const emailId = document.querySelector(".email-id");
  const password = document.querySelector("#address");
  const btnBack = document.getElementById("btn-back");
  const backgroudBlur = document.querySelector(".backgroud-blur");
  const registration_backBtn = document.getElementById(
    "new_registration_backBtn"
  );

  const isloggedin = $("#isloggedin").val();
  const cta = document.getElementById("cta");
  const popUp = document.getElementById("pop-up");
  const new_registration_cta = document.getElementById("new_registration_cta");
  const new_registration_popUp = document.getElementById("new_registration");
  const paymentpopup = document.getElementById("paymentpopup");
  const ctapayment = document.getElementById("ctapayment");
  const btnblue = document.getElementById("btnblue");
  const btnback1 = document.getElementById("btn-back1");
  // const btnback2 = document.getElementById("btn-back2");
  const endresult = document.querySelector(".endresult");
  const alertpopup = document.getElementById("alertpopup");
  const ctaalert = document.getElementById("ctaalert");
  // const newregistrationcontinue = document.getElementById(
  //   "new_registrationcontinue"
  // );
  let islog = Number(isloggedin);

  const loginForm = document.getElementById("loginForm");
  const loginpopup = document.getElementById("loginpopup");
  const loginError = document.getElementById("loginError");
  const registrationForm = document.getElementById("registrationForm");
  const registrationpopup = document.getElementById("registrationpopup");
  const usernameError = document.getElementById("reg_username-error");
  const firstnameError = document.getElementById("reg_firstname-error");
  const lastnameError = document.getElementById("reg_lastname-error");
  const emailError = document.getElementById("reg_email-error");
  const passwordError = document.getElementById("reg_password-error");
  const errorPhone = document.getElementById("reg_phone-error");
  const alreadyPaid = document.getElementById("alreadypaidpopup");

  function errorAlert(message) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Something went wrong!",
    });
  }
  function successAlert(message, callback) {
    Swal.fire({
      title: message,
      icon: "success",
    }).then((result) => {
      if (result.isConfirmed) callback();
    });
  }
  // newregistrationcontinue.addEventListener("click", () => {
  //   paymentpopup.classList.remove("slideout-animation");
  //   ctapayment.classList.add("slidein-animation");
  //   paymentpopup.style.display = "flex";
  //   new_registration_back();
  // });

  // endresult.addEventListener("click", () => {
  //   alertpopup.classList.remove("slideout-animation");
  //   ctaalert.classList.add("slidein-animation");
  //   alertpopup.style.display = "flex";
  //   NewpaymentClosePopup();
  //   closePopUpHandler();
  //   setTimeout(() => {
  //     handelalert();
  //     window.location.href = "http://yislms.com/lms/course/view.php?id=2";
  //   }, 1500);
  // });

  const handelalert = () => {
    alertpopup.classList.remove("slideout-animation");
    ctaalert.classList.add("slidein-animation");
    alertpopup.style.display = "none";
  };

  function showPopUpHandler() {
    // backgroudBlur.style.display = "block";
    if (bought == 1) {
      alreadyPaidPopup();
    } else if (!islog) {
      popUp.classList.remove("slideout-animation");
      cta.classList.add("slidein-animation");
      popUp.style.display = "flex";
    } else {
      Newpaymentpopup();
    }
  }

  function new_registration_Popup() {
    new_registration_popUp.classList.remove("slideout-animation");
    new_registration_cta.classList.add("slidein-animation");
    new_registration_popUp.style.display = "flex";
    closePopUpHandler();
  }

  function new_registration_back() {
    new_registration_cta.classList.remove("slidein-animation");
    new_registration_popUp.classList.add("slideout-animation");
    new_registration_popUp.style.display = "none";
  }

  function Newpaymentpopup() {
    paymentpopup.classList.remove("slideout-animation");
    ctapayment.classList.add("slidein-animation");
    paymentpopup.style.display = "flex";
    closePopUpHandler();
  }
  function NewpaymentClosePopup() {
    paymentpopup.classList.remove("slideout-animation");
    ctapayment.classList.add("slidein-animation");
    paymentpopup.style.display = "none";
  }

  function closePopUpHandler() {
    cta.classList.remove("slidein-animation");
    popUp.classList.add("slideout-animation");
    popUp.style.display = "none";
  }

  const handelLogin = () => {
    new_registration_back();
    // showPopUpHandler();
    popUp.classList.remove("slideout-animation");
    cta.classList.add("slidein-animation");
    popUp.style.display = "flex";
  };

  // const loginSuccessPopup = () => {
  //   closePopUpHandler();
  //   loginpopup.classList.remove("slideout-animation");
  //   loginpopup.classList.add("slidein-animation");
  //   loginpopup.style.display = "flex";
  // };

  const closeLoginSuccessPopup = (paid = false) => {
    // loginpopup.style.display = "none";
    // console.log(paid);
    if (paid) {
      alreadyPaidPopup();
    } else {
      Newpaymentpopup();
    }
  };

  const alreadyPaidPopup = () => {
    closePopUpHandler();
    window.location.href = baseUrl + "/course/view.php?id=" + courseid.value;
  };

  const registrationSuccessPopup = () => {
    closePopUpHandler();
    new_registration_back();
    registrationpopup.classList.remove("slideout-animation");
    registrationpopup.classList.add("slidein-animation");
    registrationpopup.style.display = "flex";
  };

  const closeregistrationSuccessPopup = () => {
    // registrationpopup.classList.remove("slidein-animation");
    // registrationpopup.classList.add("slideout-animation");
    // registrationpopup.style.display = "none";
    new_registration_back();
    Newpaymentpopup();
  };

  // Registration Username validation
  function validateUsername(username) {
    const usernameRegex = /^[a-zA-Z0-9]{3,16}$/;
    if (!usernameRegex.test(username)) {
      usernameError.innerText = "Username must be alphanumeric";
      return false;
    } else {
      // Username is valid
      usernameError.innerText = "";
      return true;
    }
  }

  // Registration Firstname | Lastname validation
  const validateName = (firstName, lastName) => {
    const nameRegex = /^[a-zA-Z0-9]{3,16}$/;
    if (!nameRegex.test(firstName)) {
      firstnameError.innerText = "Firstname must be alphanumeric";
    } else {
      // Firstname is valid
      firstnameError.innerText = "";
    }
    if (!nameRegex.test(lastName)) {
      lastnameError.innerText = "Lastname must be alphanumeric";
    } else {
      // Lastname is valid
      lastnameError.innerText = "";
    }
    if (
      firstnameError.innerText.length > 0 ||
      lastnameError.innerText.length > 0
    ) {
      return false;
    }
    return true;
  };

  // Registration Password validation
  function validatePassword(password) {
    const passwordRegex =
      /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/;
    let errorMessage = "";
    if (!passwordRegex.test(password)) {
      errorMessage = "Password must contain at least:\n";
      if (!/[A-Z]/.test(password)) errorMessage += "- One uppercase letter\n";
      if (!/[a-z]/.test(password)) errorMessage += "- One lowercase letter\n";
      if (!/[0-9]/.test(password)) errorMessage += "- One number\n";
      if (!/[#?!@$%^&*-]/.test(password))
        errorMessage += "- One special character\n";
      passwordError.innerText = errorMessage;
      return false;
    } else {
      // Password is valid
      passwordError.innerText = "";
      return true;
    }
  }

  new_registration_btn.addEventListener("click", new_registration_Popup);
  btnPayment.addEventListener("click", showPopUpHandler);
  btnBack.addEventListener("click", (e) => {
    e.preventDefault();
    closePopUpHandler();
  });
  registration_backBtn.addEventListener("click", new_registration_back);
  // btnblue.addEventListener("click", Newpaymentpopup);
  login1.addEventListener("click", handelLogin);
  // btnback1.addEventListener("click", NewpaymentClosePopup);

  $("#loginForm").on("submit", function (event) {
    event.preventDefault();

    let formData = $(this).serializeArray();
    let formType = formData[2].value;
    formData.pop();
    $.ajax({
      url: baseUrl + "/local/payment/index.php",
      method: "post",
      data: { formData, formType, id: courseid.value },
      dataType: "json",
      async: true,
      success: function (resp) {
        if (resp.login) {
          islog = 1;
          loginError.innerText = "";
          userid.value = resp.userid;
          // pay_username.value = resp.username;
          // callback_url.value += "&userid=" + userid.value;
          successAlert("Logged in Sucessfully", closeLoginSuccessPopup);
        } else if (resp.error) {
          errorAlert(resp.error);
        }
      },
      error: function (xhr, status, error) {
        console.log("Error:", error);
      },
    });
  });

  $("#registrationForm").on("submit", function (event) {
    event.preventDefault();
    let formData = $(this).serializeArray();
    const data = {};
    formData.forEach((item) => {
      data[item.name] = item.value;
    });

    data.id = courseid.value;
    let error = [];
    if (!validateUsername(data.username)) {
      error.push("username");
    }
    if (!validateName(data.firstname, data.lastname)) {
      error.push("name");
    }
    if (!validatePassword(data.password)) {
      error.push("password");
    }

    // Phone Validation
    const phone = document.querySelector("#phone");
    const iti = intlTelInput(phone);
    if (!iti.isValidNumber()) {
      error.push("phone");
      if (iti.getValidationError() == 1) {
        errorPhone.innerText = "Invalid Country Code!";
      } else if (iti.getValidationError() == 2) {
        errorPhone.innerText = "Phone number is too short!!";
      } else if (iti.getValidationError() == 3) {
        errorPhone.innerText = "Phone number is too long!!";
      } else if (iti.getValidationError() == 4) {
        errorPhone.innerText = "Only Locally Possible!";
      } else if (iti.getValidationError() == 5) {
        errorPhone.innerText = "Invalid Length!";
      } else {
        errorPhone.innerText = "Please enter a valid phone number.";
      }
    } else {
      errorPhone.innerText = "";
    }
    if (error.length == 0) {
      $.ajax({
        url: baseUrl + "/local/payment/index.php",
        method: "post",
        data,
        dataType: "json",
        async: true,
        success: function (resp) {
          if (resp.username_error) {
            usernameError.innerText = "Username already exists!";
          } else {
            usernameError.innerText = "";
          }
          if (resp.email_error) {
            emailError.innerText = "Email ID already exists!";
          } else {
            emailError.innerText = "";
          }
          if (resp.created) {
            userid.value = resp.userid;
            // pay_username.value = resp.username;
            // callback_url.value += "&userid=" + userid.value;
            successAlert(
              "User Registered Sucessfully!",
              closeregistrationSuccessPopup
            );
          }
        },
        error: function (xhr, status, error) {
          console.log("Error:", error);
        },
      });
    }
  });

  $(".check").on("click", function () {
    let couponCode = $("#couponCode").val();
    let discount = $("#discount");
    let finalAmount = $("#finalAmount");
    $.ajax({
      url: baseUrl + "/local/payment/index.php",
      method: "post",
      data: { coupon: couponCode, id: courseid.value },
      dataType: "json",
      async: true,
      success: function (response) {
        if (response.success) {
          discount.text("-" + response.discount + "%");
          finalAmount.text(response.finalAmount);
          amount.value = response.finalAmount * 100;
          invalidCoupon.css("display", "none");
          appliedDiscount.css("display", "flex");
          couponid.value = response.couponid;
          // callback_url.value += "&couponid=" + response.couponid;
        } else {
          invalidCoupon.css("display", "block");
          appliedDiscount.css("display", "none");
          amount.value = response.amount * 100;
          finalAmount.text(response.amount);
        }
      },
    });
  });
  let payment = new PaylinkPayments({ mode: "test" });

  const loadPaylinkForm = async () => {
    try {
      // Display Loading while the popup is initialising
      // backgroudBlur.style.display = "block";
      await payment.initPayment(
        "#cardNumber",
        "#pay_username",
        "#cardYear",
        "#cardMonth",
        "#cardcvv",
        (error) => {
          console.log("payment error" + (error.reason ? error.reason : error));
        }
      );

      // Hide loading
      // backgroudBlur.style.display = "none";
    } catch (error) {
      console.log("payment error" + error);
    } finally {
      backgroudBlur.style.display = "none";
    }
  };

  loadPaylinkForm();

  $("#submit_button_id").click(async function (event) {
    // Prevent default browser behavior
    event.preventDefault();
    var form_data = $("#form_id").serializeArray();
    let data = {};
    form_data.forEach((item, index) => {
      data[item.name] = item.value;
    });
    delete data.coupon;
    const formType = $("#form_id").data("type");

    if (formType == "moyasar") {
      data.callback_url += "&couponid=" + couponid.value;
      $.ajax({
        url: "https://api.moyasar.com/v1/payments",
        type: "POST",
        data: data,
        dataType: "json",
        error: function (data) {
          console.log(data);
          alert(data.responseJSON.message);
          // location.reload();
        },
        success: function (data) {
          console.log(data);
          var paymentId = data.id;
          window.location.href = data.source.transaction_url;
        },
      });
    } else if (formType == "paylink") {
      data.courseid = courseid.value;
      data.userid = userid.value;
      data.couponid = couponid.value;
      backgroudBlur.style.display = "block";
      $.ajax({
        url: baseUrl + "/local/payment/paylink/paylink.php",
        type: "POST",
        data: data,
        dataType: "json",
        error: function (data) {
          alert(data.responseJSON.message);
        },
        success: function (data) {
          console.log(data);

          if (data.success) {
            payment.submitInvoice(data.transaction).then((response) => {
              console.log("send payment response", response);
              alert(response.reason);
            });
          }
        },
      });
    }
  });

  // TAP PAYMENT --------------------------------------------------------------------------

  //pass your public key from tap's dashboard
  var tap = Tapjsli("pk_test_EtHFV4BuPQokJT6jiROls87Y");
  // var tap = Tapjsli("sk_test_XKokBfNWv6FIYuTMg5sLPjhJ");

  var elements = tap.elements({});
  var style = {
    base: {
      color: "#535353",
      lineHeight: "18px",
      fontFamily: "sans-serif",
      fontSmoothing: "antialiased",
      fontSize: "16px",
      "::placeholder": {
        color: "rgba(0, 0, 0, 0.26)",
        fontSize: "15px",
      },
    },
    invalid: {
      color: "red",
    },
  };
  // input labels/placeholders
  var labels = {
    cardHolder: "Card Holder Name",
    cardNumber: "Card Number",
    expirationDate: "MM/YY",
    cvv: "CVV",
  };
  //payment options
  var paymentOptions = {
    currencyCode: ["KWD", "USD", "SAR"], //change the currency array as per your requirement
    labels: labels,
    TextDirection: "ltr", //only two values valid (rtl, ltr)
    paymentAllowed: ["VISA", "MASTERCARD", "AMEX", "MADA"], //default string 'all' to show all payment methods enabled on your account
  };
  //create element, pass style and payment options
  var tapCard = elements.create("card", { style: style }, paymentOptions);
  //mount element
  tapCard.mount("#element-container");
  //card change event listener
  tapCard.addEventListener("change", function (event) {
    if (event.loaded) {
      //If ‘true’, js library is loaded
      // console.log("UI loaded :" + event.loaded);
      // console.log("current currency is :" + card.getCurrency());
    }
    var displayError = document.getElementById("error-handler");
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = "";
    }
  });

  // Handle form submission
  var tapForm = document.getElementById("form-container");
  tapForm.addEventListener("submit", function (event) {
    event.preventDefault();

    backgroudBlur.style.display = "block";

    tap.createToken(tapCard).then(function (result) {
      console.log(result);
      if (result.error) {
        // Inform the user if there was an error
        var errorElement = document.getElementById("error-handler");
        errorElement.textContent = result.error.message;
      } else {
        // Send the token to your server
        // var errorElement = document.getElementById("success");
        // errorElement.style.display = "block";
        // var tokenElement = document.getElementById("token");
        // tokenElement.textContent = result.id;
        tapTokenHandler(result.id);
      }
    });
  });

  function tapTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById("payment-form");
    var hiddenInput = document.createElement("input");
    hiddenInput.setAttribute("type", "hidden");
    hiddenInput.setAttribute("name", "tapToken");
    hiddenInput.setAttribute("value", token);
    form.appendChild(hiddenInput);
    const userid = $("#userid").val();
    const courseid = $("#courseid").val();
    const couponid = $("#couponid").val();
    const amount = $("#amount").val();
    const data = {
      userid,
      courseid,
      couponid,
      amount,
      token,
    };

    $.ajax({
      url: "https://yislms.com/lms/local/payment/tap/tap.php",
      type: "POST",
      data: data,
      dataType: "json",
      error: function (data) {
        console.warn(data);
      },
      success: function (data) {
        if (data.success) {
          window.location.href = data.url;
        }
      },
    });
  }
});
