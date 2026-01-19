import Bg from '../../assets/images/new-page-resources.png'

import styled from "styled-components";
const Wrapper = styled.div`
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
    
  .due-box .due-item.pay-btn .due-head {
    background-image: url(${Bg});
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

  .card-icon {
    width: 70px;
    height: auto;
    object-fit: contain;
  }

  .card-container {
    margin-left: -12px;
  }

  .add-btn {
    padding-top: 20px;
  }

  .add-btn span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .add-btn .action span {
    font-size: 1.5em;
  }

  .add-btn span .icn {
    font-size: 1.3em;
  }

  .radion-ops {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-bottom: 20px;
  }

  .radion-ops .rd-ops {
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .close {
    position: absolute;
    top: 20px;
    right: 20px;
  }

  .popup-title {
    position: absolute;
    top: 20px;
    left: 30px;
    font-size: 1.2em;
    font-weight: 600;
  }

  .psw-view {
    position: absolute;
    font-size: 1.3em;
    top: 36px;
    right: 8px;
    cursor: pointer;
  }

  .member .insidelabelmain {
    color: #000;
  }

  .gift-membership #selectedMembers {
    margin-bottom: 0px;
    padding-top: 20px;
    display: flex;
    flex-wrap: wrap;
    max-width: none;
  }

  .gift-membership #selectedMembers .ech-mbr {
    width: calc(50% - 10px);
    margin: 0px 5px 10px;
    flex-direction: column;
  }
  .gift-membership #selectedMembers .ech-mbr .avatar-sec {
    width: 40px;
    height: 40px;
  }

  .gift-membership #selectedMembers .ech-mbr .avatar-sec .no-img .icn {
    font-size: 22px;
  }

  .gift-membership #selectedMembers .ech-mbr .mbr-nam,
  .gift-membership #selectedMembers .ech-mbr .action {
    margin-left: 10px;
    font-size: 0.9em;
  }

  .gift-membership #selectedMembers .ech-mbr .action {
    position: absolute;
    top: -10px;
    right: -50px;
  }

  .gift-membership #selectedMembers .ech-mbr .info-sec {
    position: relative;
    margin-bottom: 10px;
  }

  .gift-membership #selectedMembers .ech-mbr .info-sec .person-info {
    margin-bottom: 0px;
  }

  .gift-membership #selectedMembers .ech-mbr .usr-wrap {
    display: flex;
    position: relative;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 5px;
  }

  .gift-membership #selectedMembers .ech-mbr .usr-wrap:not(:last-child) {
    margin-bottom: 6px;
  }

  .gift-membership #selectedMembers .ech-mbr .usr-wrap .wp-lf {
    color: #a9a9a9;
    font-size: 0.85em;
  }

  .gift-membership #selectedMembers .ech-mbr .usr-wrap .wp-rg {
    word-break: break-word;
    font-size: 0.9em;
  }

  .form-col.sugg {
    position: relative;
  }

  .form-col.sugg .suggestion-box {
    position: absolute;
    width: 100%;
    // height: 300px;
    max-height: 300px;
    overflow-y: auto;
    overflow-x: hidden;
    z-index: 1;
    background-color: #f3f3f3;
    top: 70px;
    left: 0px;
    border-radius: 6px;
  }

  .form-col.sugg .suggestion-box .suggestions {
    padding: 6px 10px;
    font-size: 0.9em;
    cursor: pointer;
  }

  .form-col.sugg .suggestion-box .suggestions:not(:last-child) {
    border-bottom: 1px solid #ececec;
  }

  .ch-btn-sec {
    display: flex;
    gap: 10px;
  }

  .order-summery {
    position: relative;
    padding-top: 30px;
  }

  .order-summery .order-box {
    border: 1px solid #e7e7e7;
    padding: 40px;
    border-radius: 8px;
    margin-bottom: 12px;
  }

  .order-summery .order-box .order-itm {
    display: flex;
    flex-direction: column;
  }

  .order-summery .order-box .order-itm:not(:last-child) {
    margin-bottom: 20px;
  }

  .order-summery .order-box .order-itm:not(:first-child) {
    border-top: 1px solid #e7e7e7;
    padding-top: 20px;
  }

  .order-summery .order-box .order-itm .ordr-membship {
    font-size: 1.3em;
    margin-bottom: 10px;
  }

    .order-summery .order-box .order-itm .ordr-membship .imp-note {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: .7em;
        color: #9a9a9a;
        flex-wrap: nowrap;
        justify-content: flex-start;
    }

    .order-summery .order-box .order-itm .ordr-membship .imp-note .icn {
        font-size: 1.2em;
    }

  .order-summery .order-box .order-itm .ordr-sub {
    margin-left: 15px;
  }

  .order-summery .order-box .order-itm .sec-aff {
    display: flex;
    margin-bottom: 15px;
    position: relative;
    flex-wrap: wrap;
  }

  .order-summery .order-box .order-itm .sec-aff .sec {
    padding-right: 20px;
  }

  .order-summery .order-box .order-itm .sec-aff .ord-sa::before {
    display: flex;
    flex-direction: column;
    font-size: 0.9em;
    color: #947f98;
  }

  .order-summery .order-box .order-itm .sec-aff .ord-sa.sec::before {
    content: "Section";
  }

  .order-summery .order-box .order-itm .sec-aff .ord-sa.aff::before {
    content: "Affiliate";
  }

  .order-summery .order-box .order-itm .ord-members {
    display: flex;
    flex-wrap: wrap;
    max-width: none;
    margin: 0px -5px 15px;
  }

  .ord-members .ech-mbr {
    width: calc(25% - 10px);
    margin: 0px 5px 10px;
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    box-shadow: 0px 0px 1px 0px rgba(0, 0, 0, 0.6);
    padding: 15px;
    border-radius: 5px;
    flex-direction: column;
  }

  .ord-members .ech-mbr .info-sec {
    position: relative;
    margin-bottom: 10px;
  }

  .ord-members .ech-mbr .usr-wrap {
    display: flex;
    position: relative;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 5px;
  }

  .ord-members .ech-mbr .usr-wrap:not(:last-child) {
    margin-bottom: 6px;
  }

  .ord-members .ech-mbr .usr-wrap .wp-lf {
    color: #a9a9a9;
    font-size: 0.85em;
  }

  .ord-members .ech-mbr .usr-wrap .wp-rg {
    word-break: break-word;
    font-size: 0.9em;
  }

  .ech-mbr .info-sec .person-info {
    display: flex;
    align-items: center;
  }

  .ech-mbr .avatar-sec {
    width: 40px;
    height: 40px;
    background-color: #ccc;
    border-radius: 50%;
    overflow: hidden;
  }

  .ech-mbr .avatar-sec .no-img {
    position: relative;
    width: 100%;
    height: 100%;
  }

  .ech-mbr .avatar-sec .no-img .icn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #333;
    font-size: 22px;
  }

  .ech-mbr .mbr-nam {
    margin-left: 10px;
    font-size: 0.9em;
  }

  .order-summery .order-box .order-itm .ord-gift {
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #947f98;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec {
    position: relative;
    border-top: 1px dashed #e7e7e7;
    padding-top: 15px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .sec-lf {
    padding-right: 15px;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .sec-rg {
    flex: 0 1 400px;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .sec-lf .act-btn {
    padding: 2px 15px;
    border: 1px solid;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8em;
  }

  .order-summery
    .order-box
    .order-itm
    .ord-amnt-sec
    .sec-lf
    .act-btn.edt:hover {
    border-color: #0e6acc;
    color: #0e6acc;
  }

  .order-summery
    .order-box
    .order-itm
    .ord-amnt-sec
    .sec-lf
    .act-btn.dlt:hover {
    border-color: #d60000;
    color: #d60000;
  }

  .order-summery
    .order-box
    .order-itm
    .ord-amnt-sec
    .sec-lf
    .act-btn:first-child {
    margin-right: 10px;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 400px;
    // margin-left: auto;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec + .amnt-sec {
    padding-top: 10px;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec .sec-label {
    font-size: 0.9em;
    flex: 0 1 200px;
    color: #947f98;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec .sec-value {
    text-align: right;
  }

  .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec .amnt {
    font-size: 1.2em;
    font-weight: 500;
  }

  .order-summery .order-ttl-charge {
    display: flex;
    // margin-top: 30px;
    justify-content: space-between;
    align-items: flex-end;
    //max-width: 445px;
    // margin-left: auto;
    // margin-bottom: 15px;
    margin: 30px 0px 15px auto;
  }

  .order-summery .order-ttl-charge .ttl-left {
    display: flex;
    flex-direction: column;
    padding-right: 10px;
  }

  .order-summery .order-ttl-charge .ttl-left .lf-lbl {
    font-size: 0.9em;
  }

  .order-summery .order-ttl-charge .ttl-left .lf-amnt {
    font-size: 1.5em;
    font-weight: 600;
  }

  .order-summery .order-ttl-charge .ttl-right {
    display: flex;
    align-items: center;
  }

  .order-summery .ordr-chk-box .chk-label {
    display: flex;
    align-items: baseline;
    gap: 5px;
    }

  .brdcrb-cursor {
    cursor: pointer;
  }

  .rcv-sts {
    color: #925da1;
  }

  .addr-label {
    margin-bottom: 15px;
    padding-top: 10px;
    font-size: 1.2em;
  }

  .puritm-popup .containers {
    padding-top: 20px;
  }

  .puritm-popup .each-itm {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 20px;
  }

    .puritm-popup .each-itm:last-child {
        margin-bottom: 0;
    }

    .puritm-popup .each-itm .itm-name {
        font-weight: 500;
    }
    
    .puritm-popup .each-itm .itm-btn {
        width: 65px;
    }

    .puritm-popup .each-itm .itm-btn .sel-btn {
        padding: 5px 15px;
        min-width: unset;
    }

  @media only screen and (max-width: 1260px) {
    .ord-members .ech-mbr {
      width: calc(33.33% - 10px);
    }
  }

  @media only screen and (max-width: 1260px) and (min-width: 991px) {
    .hist-item .gifted-wrapper {
        font-size: .9em;
    }
  }

  @media only screen and (max-width: 839px) {
    .form-row {
      flex-direction: column;
    }

    .form-row .form-col {
      width: calc(100% - 0px);
    }

    .form-row .form-col:first-child {
      margin-bottom: 10px;
    }

    .form-row .form-col + .form-col {
      margin-left: 0px;
    }
  }

  @media only screen and (max-width: 768px) {
    .ord-members .ech-mbr {
      width: calc(50% - 10px);
    }

    .order-summery .order-box {
      padding: 20px;
    }

    .hist-item .gifted-wrapper {
        font-size: .9em;
    }
  }

  @media only screen and (max-width: 609px) {
    .ord-members .ech-mbr {
      width: calc(100% - 10px);
    }
  }

  @media only screen and (max-width: 570px) {
    .order-summery .order-box .order-itm .ord-amnt-sec {
      flex-direction: column-reverse;
      align-items: normal;
    }

    .order-summery .order-box .order-itm .ord-amnt-sec .sec-lf {
      padding-top: 20px;
    }

    .order-summery .order-box .order-itm .ord-amnt-sec .sec-rg {
      flex: none;
    }

    .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec {
      max-width: 100%;
    }
  }

  @media only screen and (max-width: 425px) {
    .gift-membership #selectedMembers {
      flex-direction: column;
    }

    .gift-membership #selectedMembers .ech-mbr {
      margin: 0px 5px 10px;
      width: auto;
    }

    .order-summery .order-box .order-itm .ordr-sub {
      margin-left: 0px;
    }
  }

  @media only screen and (max-width: 375px) {
    .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec {
      display: block;
    }

    .order-summery .order-box .order-itm .ord-amnt-sec .amnt-sec .sec-value {
      text-align: left;
    }

    .order-summery .order-ttl-charge {
      flex-direction: column;
      align-items: normal;
    }

    .order-summery .order-ttl-charge .ttl-left {
      margin-bottom: 15px;
    }

    .order-summery .order-ttl-charge .ttl-right .btn {
      border-radius: 10px;
    }
  }
`;
export default Wrapper;
