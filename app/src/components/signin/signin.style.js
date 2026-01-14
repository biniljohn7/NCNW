import styled from 'styled-components'
import { HEADER_COLOR } from '../../helper/constant'

const SigninWrapper = styled.form`
  .cancelmain {
    width: 20px;
    height: 20px;
    // float: right;
    position: absolute;
    right: 15px;
    top: 15px;
  }

  .short-desc {
    letter-spacing: 1px;
  }

  .flex-container {
    display: flex;
    flex-direction: column;
    text-align: center;
    justify-content: center;
  }

  .flex-item {
    flex: 50%;
  }

  // .login {
  //   background-color: ${HEADER_COLOR};
  // }

  row {
    margin-right: 0px;
    margin-left: 0px;
  }

  .pwd {
    position: absolute;
    right: 15px;
    top: 36%;
  }

  .inp-row {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .login-sec {
    margin: 0 auto;    
    padding: 40px 0px;
  }
  .login-sec .top-head {
    text-align: center;
    color: #5B2166;
    text-transform: uppercase;
    font-size: 20px;
    line-height: 30px;
    font-weight: 400;
  }
  .login-sec .top-head-sub {
    font-size: 0.7em;
    padding-top: 10px;
    margin-bottom: 40px;
    font-style: italic;
  }
  .login-sec .form-sec {
    border: 1px solid #5B2166;
    border-radius: 50px;
    padding-bottom: 30px;
  }
  .login-sec .main-head {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    background-color: #5B2166;
    border-radius: 50px;
    color: #fff;
    font-size: 48px;
    line-height: 72px;
    font-weight: 700;
    height: 110px;
  }
  .login-sec .inputmain {
    color: #5B2166;
    border: 1px solid #5B2166;
    border-radius: 100px;
    font-size: 16px;
    padding: 20px 25px !important;
    outline: none;
  }
  .login-sec .inputmain::-ms-reveal {
    display: none;
  }
  .login-sec .inputmain::-ms-clear {
    display: none;
  }
  .login-sec .inputmain::placeholder {
    color: #5B2166;
    opacity: 1;
  }
  .login-sec .red--text {
    color: #5B216680 !important;
    opacity: 75%;
    font-size: 16px;
    font-weight: 400;
    text-decoration: underline;
  }
  .login-sec .button {
    width: 170px;
    max-width: 100%;
  }
  .sgp-area {
    text-align: center;
    border-top: 1px solid #ccc;
    margin-top: 30px;
    padding-top: 30px;
    
    .sgp-ttl {
      color: #333;
      font-weight: 700;
      margin-bottom: 8px;
    }
  
    .sgp-btn-bx a {
      text-decoration: underline;
    }
  
    .sgp-btn-bx a:hover {
      text-decoration: none;
    }
  }

`

export default SigninWrapper
