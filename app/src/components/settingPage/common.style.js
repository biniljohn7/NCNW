import styled from 'styled-components'

const CommonWrapper = styled.div`
.go2139093995 {
  --rmsc-main:#4285f4;
  --rmsc-hover:#f1f3f5;
  --rmsc-selected:#e2e6ea;
  --rmsc-border:#ccc;
  --rmsc-gray:#aaa;
  --rmsc-bg:#fff;
  --rmsc-p:10px;
  --rmsc-radius:4px;
  --rmsc-h:38px;
}
  .expiry-alert{
    color:#FFF;
    font-weight:bold;
    background-color:#cf1818;
    a{
      color:#fff;
      text-decoration:underline;
    }
  }
  .cancelmain {
    width: 20px;
    height: 20px;
    // float: right;
    position: absolute;
    right: 15px;
    top: 15px;
  }

  .pwd {
    position: absolute;
    right: 15px;
    top: 56%;
  }

  .flex-container {
    display: flex;
    flex-direction: column;
    text-align: center;
    justify-content: center;
    background-color: #f5f4f4;
    // background-color: #e7efff;
    padding: 20px 10px;
  }

  .flex-item {
    flex: 50%;
  }

    .flex-item .admin-panel-link {
        position: relative;
        color: #2552ff;
        cursor: pointer;
        text-decoration: none;
        text-decoration-color: transparent; /* start invisible */
        transition: text-decoration-color .3s ease-in-out;
    }

    .flex-item .admin-panel-link:hover {
        text-decoration: underline;
        text-decoration-color: #2552ff; /* animate in */
    }

  .camera {
    top: 29px;
    right: 5px;
  }

  .delete {
    position: absolute;
    right: 10px;
    top: 10px;
  }

  .profile_pic {
    height: 122px;
    width: 122px;
    border-radius: 50%;
    // object-fit: contain;
  }

  .mamber-pic {
    position: relative;
    height: 122px;
  }

  .star-icn {
    right: 25px;
    bottom: 5px
  }

  .active {
    background-color: #f5f4f4;
    // background-color: #e7efff;
  }

  .content-padding {
    padding: 15px 15px 11px 15px;
  }

  .react-switch {
    position: absolute !important;
    right: 7px;

    .react-switch-handle {
      height: 20px !important;
      width: 20px !important;
      top: -2.5px !important;
    }
  }

  .profile-switch {
    position: absolute !important;
    right: 0px !important;

    .react-switch-handle {
      height: 20px !important;
      width: 20px !important;
      top: -2.5px !important;
    }
  }

  .date-picker {
    background: #ffffff 0% 0% no-repeat padding-box;
    border: 1px solid rgba(112, 112, 112, 0.4);
    border-radius: 4px;
    opacity: 1;
  }

  .date-picker:focus {
    outline: none;
  }

  .file-upload {
    position: relative;
    display: block;
  }

  .file-upload__input {
    position: absolute;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    font-size: 1;
    width: 0;
    height: 100%;
    opacity: 0;
  }

  .openBtn {
    cursor: pointer;
    font-size: 20px;
    padding: auto;
    border: none;
  }

  .sidenav {
    height: 100%;
    width: 0;
    position: fixed;
    z-index: 201;
    top: 0;
    left: 0;
    background-color: #ffffff;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 60px;
  }
  .sidenav .navMenu {
    padding: 3px 10px 10px 7px;
    text-decoration: none;
    font-size: 16px;
    display: block;
    transition: 0.5s;
    cursor: pointer;
    color: #000000;
  }
  .sidenav .navMenu:hover {
    color: #f1f1f1;
  }
  .sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
    cursor: pointer;
  }

  .close {
    position: absolute;
    top: 20px;
    right: 20px;
  }

  .float-right {
    float: right;
  }

  .form-group {
    margin-bottom: 1rem;
  }

  .org-sub-ttl {
    color: #8d8d8d !important;
  }

  .mb-wrap {
    padding-top: 20px;
  }

  .mb-item {
    position: relative;
    max-width: 580px;
  }

  .mb-card {
    display: flex;
    flex-direction: column;
  }

  .mb-card .mb-details {
    margin-bottom: 20px;
    padding-left: 40px;
    color: #000;
  }

  .mb-card .mb-details .dtl-top {
    margin-bottom: 30px;
  }

  .mb-card .mb-details .dtl-top .dtl-itms span:first-child {
    padding-right: 8px;
  }

  .mb-card .mb-details .dtl-btm {
    text-align: center;
  }

  .mb-dwnld {
    max-width: 580px;
    display: flex;
    padding-top: 25px;
    justify-content: space-around;
    gap: 10px;
  }

  .mb-dwnld .dwnld-box {
    padding: 10px 22px;
    background-color: #5b2166;
    border-radius: 30px;
    display: flex;
    align-items: center;
    gap: 5px;
    color: #fff;
    cursor: pointer;
  }

  .mb-dwnld .dwnld-btn {
    font-size: 0.9em;
    color: #fff;
  }

    .row.prf-row .sts.Active {
        color: #007809;
    }

    .row.prf-row .sts.Inactive {
        color: #de0021;
    }

    .noti .noti-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .noti .noti-list .chk-label {
        display: flex;
        align-items: center;
        gap: 6px;
    }
  .pwd-inp::-ms-reveal {
    display: none;
  }
    .pwd-inp::-ms-clear {
    display: none;
  }
  @media only screen and (max-width: 1024px) {
      .row.prf-row {
        flex-direction: column;
      }

      .row.prf-row .col-6 {
        width: 100%;
      }
  }

   @media only screen and (max-width: 509px) {
    .mb-card .mb-details {
      padding-left: 12px;
      font-size: 0.9em;
    }

    .mb-dwnld {
      flex-direction: column;
      align-items: center;
    }

    .mb-dwnld .dwnld-box {
        padding: 10px 15px;
    }
  }
  
`

export default CommonWrapper
