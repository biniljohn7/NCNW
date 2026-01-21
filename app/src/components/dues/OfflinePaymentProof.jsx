import React, { useState } from "react";
import { Modal } from "reactstrap";
import Wrapper from "./dues.style";
import Spinner from "../../UI/Spinner/Spinner";
import Toast from "../../UI/Toast/Toast";
import { uploadOfflineProof } from "../../api/duesAPI";

function OfflinePaymentProof(props) {
  const [file, setFile] = useState(null);
  const [payNum, setPayNum] = useState("");
  const fileInputRef = React.useRef(null);

  let Spn = Spinner();
  const Tst = Toast();

  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
  };

  const isValidUSPaymentNumber = (value) => {
    // checks & money orders are numeric, usually 4–10 digits
    return /^\d{4,10}$/.test(value);
  };

  const submitProof = async () => {
    if (!file) {
      Tst.Error("Please upload payment proof");
      return;
    }

    if (!payNum) {
      Tst.Error("Please enter check or money order number");
      return;
    }

    if (!isValidUSPaymentNumber(payNum)) {
      Tst.Error("Invalid check or money order number");
      return;
    }

    try {
      Spn.Show();

      const formData = new FormData();
      formData.append("paymentProof", file);
      formData.append("payNum", payNum);
      formData.append("txnId", props.txnId);

      const res = await uploadOfflineProof(formData);

      if (res.success === 1) {
        props.onSuccess && props.onSuccess(res.data);
        props.toggle();
      } else {
        Tst.Error(res.message);
      }
    } catch (err) {
      Tst.Error("Something went wrong!");
    } finally {
      Spn.Hide();
    }
  };

  return (
    <div>
      {Spn.Obj}
      {Tst.Obj}

      <Modal
        isOpen={props.isOpen}
        toggle={props.toggle}
        centered
        size="md"
        backdrop="static"
        keyboard={false}
      >
        <Wrapper>
          <div className="plr-30 ptb-40 position-relative">
            <div className="popup-title">Upload Payment Proof</div>

            <div
              className="cursor-pointer text-bold close"
              onClick={props.toggle}
            >
              X
            </div>

            <div className="form-group mt-20">
              <label>Payment Proof</label>
              <input
                ref={fileInputRef}
                type="file"
                className="form-control"
                accept="image/*,application/pdf"
                onChange={handleFileChange}
              />
            </div>

            <div className="form-group mt-20">
              <label>Check / Money Order Number</label>
              <input
                type="text"
                className="form-control"
                placeholder="Enter check or money order number"
                value={payNum}
                onChange={(e) =>
                  setPayNum(e.target.value.replace(/\D/g, ""))
                }
                maxLength={10}
              />
              <small className="text-muted">
                Checks and Money Orders only (4-10 digits)
              </small>
            </div>

            <div className="text-right mt-30">
              <button className="btn mr-10" onClick={props.toggle}>
                Cancel
              </button>

              <button className="btn btn-primary" onClick={submitProof}>
                Submit
              </button>
            </div>
          </div>
        </Wrapper>
      </Modal>
    </div>
  );
}

export default OfflinePaymentProof;
