import React, { useState } from "react";
import { Modal } from "reactstrap";
import Wrapper from "./dues.style";
import Spinner from "../../UI/Spinner/Spinner";
import Toast from "../../UI/Toast/Toast";
import { uploadOfflineProof } from "../../api/duesAPI";

function OfflinePaymentProof(props) {
  const [file, setFile] = useState(null);
  const [remarks, setRemarks] = useState("");
  const fileInputRef = React.useRef(null);

  let Spn = Spinner();
  const Tst = Toast();

  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
  };

  const submitProof = async () => {
    if (!file) {
      Tst.Error("Please upload payment proof");
      return;
    }

    try {
      Spn.Show();

      const formData = new FormData();
      formData.append("paymentProof", file);
      formData.append("remarks", remarks);
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
              <label>Remarks (optional)</label>
              <textarea
                className="form-control"
                rows="3"
                value={remarks}
                onChange={(e) => setRemarks(e.target.value)}
              />
            </div>

            <div className="text-right mt-30">
              <button
                className="btn btn-secondary mr-10"
                onClick={props.toggle}
              >
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
