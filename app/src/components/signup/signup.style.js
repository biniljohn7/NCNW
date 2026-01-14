import styled from "styled-components";
// import { HEADER_COLOR } from "../../helper/constant";

const SigninWrapper = styled.div`
  .sgp-container {
    width: 1020px;
    max-width: 100%;
    padding: 60px 15px;
    box-sizing: border-box;
    margin: 0 auto;
  }
  .pwd {
    position: absolute;
    right: 15px;
    top: 62px;
  }
  .ttl-1 {
    text-align: center;
    color: #5b2166;
    font-size: 20px;
  }

  .ttl-1-sub {
    font-size: 0.7em;
    padding-top: 10px;
    margin-bottom: 40px;
    font-style: italic;
  }

  .ttl-2 {
    text-align: center;
    color: #5b2166;
    font-size: 48px;
    font-weight: 700;
    padding-top: 16px;
    margin-bottom: 40px;

    @media screen and (max-width: 767px) {
      font-size: 30px;
    }
  }
  .form-area {
    display: flex;

    @media screen and (max-width: 767px) {
      display: block;
    }
  }

  .form-area .form-col {
    width: calc(50% - 20px);
    @media screen and (max-width: 767px) {
      width: auto;
    }
  }

  .form-area .form-col + .form-col {
    margin-left: 20px;
    @media screen and (max-width: 767px) {
      margin-left: 0;
      margin-top: 20px;
    }
  }

  .form-area .form-col .fm-row + .fm-row {
    margin-top: 20px;
  }

  .form-area .form-col .fm-row .insidelabelmain {
    color: #5b2166;
    font-size: 24px;
    margin-bottom: 8px !important;
    display: block;
    height: auto;
  }

  .form-area .form-col .inputmain {
    border-color: #5b2166;
    border-radius: 32px;
    color: rgba(91, 33, 102, 1);
    padding: 16px 26px !important;
    font-size: 16px;
  }
  .form-area .form-col .inputmain::-ms-reveal {
    display: none;
  }
  .form-area .form-col .inputmain::-ms-clear {
    display: none;
  }
  .form-area .form-col .inputmain.multiselect {
    border: 1px solid;
    background-color: #fff;
    z-index: 1;
  }

  .form-area .form-col .inputmain.multiselect .dropdown-content {
    background-color: #fff;
    z-index: 99999;
    padding:0 10px
  }

  .form-area .form-col .inputmain::placeholder {
    color: #5b2166;
  }

  .sgp-agree {
    text-align: center;
    font-size: 20px;
    padding: 40px 0;
  }

  a {
    color: #5b2166;
    font-weight: 500;
  }
  a:hover {
    text-decoration: underline;
  }
  .submit-area {
    text-align: center;
  }
  .submit-area button {
    width: 211px;
    max-width: 100%;
    padding: 12px !important;
  }

  .form-area .form-col .fm-row .pws-rule {
    font-size: 0.56em;
    color: #4b4d52;
    font-weight: 300;
    padding-top: 8px;
    position: absolute;
  }
`;

export default SigninWrapper;
