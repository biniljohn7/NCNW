import React, { useEffect, useState } from "react";
import Wrapper from "./dues.style";
import Select from "react-select";
import Switch from "react-switch";
import Input from "../../UI/input/input";
import {
  getMembershipPlans,
  getInstallments,
  duesSearchSections,
  duesSearchAffiliate,
  addMembership,
} from "../../api/duesAPI";
import { connect } from "react-redux";
import AuthActions from "../../redux/auth/actions";
import { ToastsStore } from "react-toasts";
import { Breadcrumb, BreadcrumbItem } from "reactstrap";
import { Link } from "react-router-dom";
import { compose } from "redux";
import { chooseMembership as enhancer } from "./enhancer";
import Spinner from "../../UI/Spinner/Spinner";
import Toast from "../../UI/Toast/Toast";
import Pix from "../../helper/Pix";
import "../../assets/css/style2.css";
import { store } from "../../redux/store";
import { MEMBERSHIP_FOR } from "../../helper/constant";
import SelectMember from "./SelectMember";
import OtherPurchasableList from "./OtherPurchasableList";

const { logout } = AuthActions;

function Membership(props) {
  const { memberId, membershipStatus } = store.getState().auth;
  const [showForm, setShowForm] = useState(false);
  const [isEdited, setIsEdited] = useState(null);
  const [dropdown, setDropdown] = useState(null);
  const [othPurLists, setOthPurLists] = useState(null);
  const [sectionsDropdown, setSectionsDropdown] = useState([]);
  const [affiliatesDropdown, setAffiliatesDropdown] = useState([]);
  const [subDropItems, setSubDrop] = useState({});
  const [isMbrOpen, setMbrOpen] = useState(false);
  const [openOthPurList, setOpenOthPurList] = useState(false);
  const [isGift, setIsGift] = useState(false);
  //   const [unique, setUnique] = useState([]);
  const [content, setContent] = useState([]);
  const [ErrorList, setErrorList] = useState({});
  const [membData, setMembData] = useState({
    membershipPlan: "",
    membershipPlanName: "",
    membershipPlanCharge: "",
    installment: 1,
    installmentName: "One-Time Payment",
    section: "",
    affiliate: "",
    membshipFor: "",
    sectionLabel: "",
    affiliateLabel: "",
  });
  const [membId, setMembId] = useState({});
  const [othId, setOthId] = useState({});
  const [membershipList, setMembershipList] = useState([]);
  const [otherPurchasableItems, setOtherPurchasableItems] = useState([]);
  const [mntlyDonation, setMntlyDonation] = useState(false);
  const [totalAmount, setTotalAmount] = useState(0);
  const [updtAmnt, setUpdtAmnt] = useState(0);
  const paymentOptions = [
    { value: "stripe", label: "Credit / Debit Card" },
    { value: "check", label: "Check" },
    { value: "moneyorder", label: "Money Order" },
  ];
  const [payMethod, setPayMethod] = useState(null);
  const lgMbr = store.getState().auth.memberId;
  let mbrExist = false;
  let othExist = false;
  let ttlAmt = 0;

  let Spn = Spinner();
  let Tst = Toast();

  useEffect(() => {
    // if (membershipStatus == "active") {
    //   setUnique([memberId]);
    // }
    getMembershipPlans()
      .then((res) => {
        setDropdown(res.data.plans);
        setOthPurLists(res.data.others);
      })
      .catch((err) => {
        if (err.response) {
          if (err.response.status === 401) {
            props.logout();
            ToastsStore.error("Session Expire! Please login again.");
            setTimeout(() => props.history.replace("/signin"), 800);
          } else {
            ToastsStore.error("Something went wrong!");
          }
        } else if (err.request) {
          ToastsStore.error("Unable to connect to server!");
        } else {
          ToastsStore.error("Something went wrong!");
        }
      });
    // Fetch sections for dropdown
    duesSearchSections("")
      .then((res) => {
        if (res.success === 1) {
          setSectionsDropdown(
            res.data.map((section) => ({
              value: section.sectionId,
              label: section.sectionName,
            }))
          );
        }
      })
      .catch(() => {
        ToastsStore.error("Failed to load sections!");
      });

    duesSearchAffiliate("")
      .then((res) => {
        if (res.success === 1) {
          setAffiliatesDropdown(
            res.data.map((affiliate) => ({
              value: affiliate.affiliateId,
              label: affiliate.affiliateName,
            }))
          );
        }
      })
      .catch(() => {})
      .finally(() => {
        Spn.Hide();
      });

    setContent([]);
    setMembData([]);
    setSubDrop({});
    setIsGift(false);
  }, []);

  document.title = "Membership - " + window.seoTagLine;

  const addMbr = (
    mbrId,
    mbrNam,
    mbrAvatar,
    mbrMembship,
    mbrZip,
    mbrCity,
    mbrMembId
  ) => {
    setMembId((prev) => {
      if (prev[mbrId]) {
        mbrExist = true;
        return prev;
      }

      return {
        ...prev,
        [mbrId]: [mbrNam, mbrAvatar, mbrMembship, mbrZip, mbrCity, mbrMembId],
      };
    });
  };

  const handleAddContent = (person) => {
    addMbr(
      person.id,
      person.name,
      person.avatarUrl,
      person.membership,
      person.zipcode,
      person.city,
      person.memberId
    );
    if (!mbrExist) {
      setContent([...content, person]);
    } else {
      Tst.Error("Member already added!");
    }
    setMbrOpen(false);
  };

  const removeMbr = (mbrId) => {
    setMembId((prev) => {
      const updated = { ...prev };
      delete updated[mbrId];
      return updated;
    });

    setContent((prev) => prev.filter((cnt) => cnt.id !== mbrId));
  };

  const Error = ({ field }) => {
    return ErrorList[field] ? (
      <div className="text-danger">{ErrorList[field]}</div>
    ) : (
      <></>
    );
  };

  const handleMembershipForm = (e) => {
    function el(id) {
      return document.getElementById(id);
    }

    let sErrs = {};

    if (!membData.membershipPlan) {
      sErrs["plan"] = "This field is required";
    }
    if (!membData.membshipFor) {
      sErrs["membshipFor"] = "This field is required";
    }
    if (isGift && content.length < 1) {
      sErrs["memberGift"] = "At least one member is required!";
    }
    // if (!(membData.section || membData.affiliate)) {
    //   sErrs["section"] = "Please select at least one: Section, Affiliation";
    // }
    // if (!membData.affiliate) {
    //   sErrs["affiliation"] = "This field is required";
    // }

    setErrorList(sErrs);
    if (Object.keys(sErrs).length < 1) {
      const formattedMembers = {
        memberIds: Object.entries(membId).map(
          ([id, [name, avatarUrl, membership, zipcode, city, memberId]]) => ({
            id: parseInt(id),
            name,
            avatarUrl,
            membership,
            zipcode,
            city,
            memberId,
          })
        ),
        membershipPlan: membData.membershipPlan,
        membershipPlanName: membData.membershipPlanName,
        membershipPlanCharge: membData.membershipPlanCharge,
        installment: membData.installment,
        installmentName: membData.installmentName,
        sectionId: membData.section,
        sectionName: membData.sectionLabel,
        affiliateId: membData.affiliate,
        affiliateName: membData.affiliateLabel,
      };
      setShowForm(false);

      if (typeof isEdited === "number" && isEdited !== null && isEdited >= 0) {
        setMembershipList((prevList) => {
          const updatedList = [...prevList];
          updatedList[isEdited] = formattedMembers;
          return updatedList;
        });
        setIsEdited(null);
      } else {
        setMembershipList((prevList) => [...prevList, formattedMembers]);
      }
      setTotalAmount(
        (prevAmount) =>
          prevAmount +
          parseFloat(
            (membData.membershipPlanCharge *
              formattedMembers.memberIds.length) /
              membData.installment || 0
          )
      );
    }
  };

  const makePayment = () => {
    if (!payMethod) {
      Tst.Error("Please select a payment method");
      return;
    }
    if (membershipList.length > 0 || otherPurchasableItems.length > 0) {
      Spn.Show();

      const membershipData = {
        members: membershipList.map((item) => ({
          memberIds: item.memberIds.map((m) => m.id),
          membershipPlan: item.membershipPlan,
          installment: item.installment,
          sectionId: item.sectionId,
          affiliateId: item.affiliateId,
        })),
        others: otherPurchasableItems.map((oth) => oth.id),
        donation: mntlyDonation,
        payMethod: payMethod,
      };
      addMembership(membershipData)
        .then((res) => {
          if (res.success === 1) {
            if (res.data) {
              if (res.data.paymentUrl) {
                window.location.href = res.data.paymentUrl;
              } else if (payMethod != "stripe") {
                window.location.href = '/dues';
              }
            } else {
              Tst.Error("Something went wrong!");
            }
          } else {
            Tst.Error(res.message);
          }
        })
        .catch((err) => {
          Tst.Error("Something went wrong!");
        })
        .finally(() => {
          Spn.Hide();
        });
    }
  };

  const removeMembship = (e, key, amount) => {
    setMembershipList((prevList) =>
      prevList.filter((_, index) => index !== key)
    );
    setTotalAmount((prevAmount) => prevAmount - parseFloat(amount || 0));
  };

  const editMembship = (iKey) => {
    let membFor = "",
      users = membershipList[iKey].memberIds;

    if (users.filter((m) => m.id != lgMbr).length > 0) {
      membFor = "gift";

      const exMemb = users.reduce((acc, curr) => {
        acc[curr.id] = [
          curr.name,
          curr.avatarUrl,
          curr.membership,
          curr.zipcode,
          curr.city,
          curr.memberId,
        ];
        return acc;
      }, {});

      setMembId(exMemb);
      setContent(users);
      setIsGift(true);
    } else {
      membFor = "myself";
      setMembId({ [lgMbr]: ["myself", ""] });
      setIsGift(false);
      setContent([]);
    }

    const modData = {
      membershipPlan: membershipList[iKey].membershipPlan,
      membershipPlanName: membershipList[iKey].membershipPlanName,
      membershipPlanCharge: membershipList[iKey].membershipPlanCharge,
      installment: membershipList[iKey].installment,
      installmentName: membershipList[iKey].installmentName,
      section: membershipList[iKey].sectionId,
      sectionLabel: membershipList[iKey].sectionName,
      affiliate: membershipList[iKey].affiliateId,
      affiliateLabel: membershipList[iKey].affiliateName,
      membshipFor: membFor,
    };

    setMembData(modData);
    setShowForm(true);
    setIsEdited(iKey);
    setUpdtAmnt(
      (prev) =>
        prev +
        parseFloat(
          (membershipList[iKey].membershipPlanCharge * users.length) /
            membershipList[iKey].installment
        )
    );

    getInstallments(membershipList[iKey].membershipPlan)
      .then((res) => {
        setSubDrop((prevDrpItems) => ({
          ...prevDrpItems,
          [lgMbr]: {
            items: res.data || [],
          },
        }));
      })
      .catch((err) => {})
      .finally(() => {
        Spn.Hide();
      });
  };

  const addOth = (id, title, amount) => {
    setOthId((prev) => {
      if (prev[id]) {
        othExist = true;
        return prev;
      }

      return {
        ...prev,
        [id]: [title, amount],
      };
    });
  };

  const handlePurchasableItem = (item) => {
    addOth(item.id, item.title, item.amount);
    if (!othExist) {
      setOtherPurchasableItems([...otherPurchasableItems, item]);
      setTotalAmount((prevAmount) => prevAmount + parseFloat(item.amount || 0));
    } else {
      Tst.Error("Item already added!");
    }
    setOpenOthPurList(false);
  };

  const removeOther = (id, amount) => {
    setOthId((prev) => {
      const otherids = { ...prev };
      delete otherids[id];
      return otherids;
    });

    setOtherPurchasableItems((prev) => prev.filter((itm) => itm.id !== id));
    setTotalAmount((prevAmount) => prevAmount - parseFloat(amount || 0));
  };
  return (
    <Wrapper>
      {Tst.Obj}
      {Spn.Obj}
      <div className="red pt-20 bread-nav">
        <div className="container">
          <Breadcrumb>
            <BreadcrumbItem>
              <Link to="/dues" className="text-white">
                Dues
              </Link>
            </BreadcrumbItem>
            <BreadcrumbItem
              className="text-white brdcrb-cursor"
              onClick={() => {
                setShowForm(false);
                setUpdtAmnt(0);
              }}
              active={!showForm}
            >
              Memberships
            </BreadcrumbItem>
            {showForm && (
              <BreadcrumbItem active className="text-white">
                Membership Form
              </BreadcrumbItem>
            )}
          </Breadcrumb>
        </div>
      </div>
      <div className="container">
        <div className="ptb-50">
          {!showForm && (
            <>
              <b>Choose Items to Include in Your Order</b>
              <br />
              <br />
              <div className="text-left ch-btn-sec">
                <button
                  className="btn btn-rounded button plr-20 ptb-10"
                  type="button"
                  onClick={() => {
                    setShowForm(true);
                    setContent([]);
                    setMembData([]);
                    setSubDrop({});
                    setIsGift(true);
                  }}
                >
                  ADD MEMBERSHIP
                </button>
                <button
                  className="btn btn-rounded button plr-20 ptb-10"
                  type="button"
                  onClick={() => {
                    setOpenOthPurList(true);
                  }}
                >
                  Sustaining Sponsorship
                </button>
              </div>
              <div className="order-summery">
                {((membershipList && membershipList.length > 0) ||
                  (otherPurchasableItems &&
                    otherPurchasableItems.length > 0)) && (
                  <>
                    <h4>Order Summary</h4>
                    <div className="order-box">
                      {membershipList.map((mbr, key) => {
                        ttlAmt += parseFloat(
                          (mbr.membershipPlanCharge * mbr.memberIds.length) /
                            mbr.installment || 0
                        );
                        return (
                          <div className="order-itm" key={key}>
                            <div className="ordr-membship">
                              {mbr.membershipPlanName || ""}
                            </div>
                            <div className="ordr-sub">
                              <div className="sec-aff">
                                {mbr.sectionName && (
                                  <div className="ord-sa sec">
                                    {mbr.sectionName || ""}
                                  </div>
                                )}
                                {mbr.affiliateName && (
                                  <div className="ord-sa aff">
                                    {mbr.affiliateName || ""}
                                  </div>
                                )}
                              </div>
                              {mbr.memberIds.filter(
                                (member) => member.id != lgMbr
                              ).length > 0 && (
                                <>
                                  <div className="ord-gift">Gift to</div>
                                  <div className="ord-members">
                                    {mbr.memberIds
                                      .filter((member) => member.id != lgMbr)
                                      .map((member, index) => (
                                        <div className="ech-mbr" key={index}>
                                          <div className="info-sec">
                                            <div className="person-info">
                                              <div className="avatar-sec">
                                                {member.avatarUrl ? (
                                                  <div className="mbr-img">
                                                    <img
                                                      src={member.avatarUrl}
                                                      alt=""
                                                    />
                                                  </div>
                                                ) : (
                                                  <div className="no-img">
                                                    <span className="material-symbols-outlined icn">
                                                      person
                                                    </span>
                                                  </div>
                                                )}
                                              </div>
                                              <div className="mbr-nam">
                                                {member.name || ""}
                                              </div>
                                            </div>
                                          </div>
                                          <div className="usr-wrap">
                                            <div className="wp-lf">
                                              Membership:
                                            </div>
                                            <div className="wp-rg">
                                              {member.membership ?? "--"}
                                            </div>
                                          </div>
                                          <div className="usr-wrap">
                                            <div className="wp-lf">
                                              Member ID:
                                            </div>
                                            <div className="wp-rg">
                                              {member.memberId ?? "--"}
                                            </div>
                                          </div>
                                          <div className="usr-wrap">
                                            <div className="wp-lf">City:</div>
                                            <div className="wp-rg">
                                              {member.city ?? "--"}
                                            </div>
                                          </div>
                                          <div className="usr-wrap">
                                            <div className="wp-lf">
                                              Zipcode:
                                            </div>
                                            <div className="wp-rg">
                                              {member.zipcode ?? "--"}
                                            </div>
                                          </div>
                                        </div>
                                      ))}
                                  </div>
                                </>
                              )}
                              <div className="ord-amnt-sec">
                                <div className="sec-lf">
                                  <span
                                    className="act-btn edt"
                                    onClick={() => editMembship(key)}
                                  >
                                    EDIT
                                  </span>
                                  <span
                                    className="act-btn dlt"
                                    onClick={(e) => {
                                      if (
                                        window.confirm(
                                          "Are you sure to remove this membership?"
                                        )
                                      ) {
                                        removeMembship(
                                          e,
                                          key,
                                          parseFloat(
                                            (mbr.membershipPlanCharge *
                                              mbr.memberIds.length) /
                                              mbr.installment || 0
                                          )
                                        );
                                      }
                                    }}
                                  >
                                    REMOVE
                                  </span>
                                </div>
                                <div className="sec-rg">
                                  <div className="amnt-sec">
                                    <div className="sec-label">Installment</div>
                                    <div className="sec-value">
                                      {mbr.installmentName || ""}
                                    </div>
                                  </div>
                                  <div className="amnt-sec">
                                    <div className="sec-label">
                                      Total Charge
                                    </div>
                                    <div className="sec-value amnt">
                                      {Pix.dollar(
                                        (mbr.membershipPlanCharge *
                                          mbr.memberIds.length) /
                                          mbr.installment || 0,
                                        1
                                      )}
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        );
                      })}
                      {otherPurchasableItems.map((oth, key) => {
                        ttlAmt += parseFloat(oth.amount || 0);
                        return (
                          <div className="order-itm" key={key}>
                            <div className="ordr-membship">
                              {oth.title || ""} <br />
                              <span className="imp-note">
                                <span className="material-symbols-outlined icn">
                                  label_important
                                </span>
                                Buy it for yourself.
                              </span>
                            </div>
                            <div className="ordr-sub">
                              <div className="ord-amnt-sec">
                                <div className="sec-lf">
                                  <span
                                    className="act-btn dlt"
                                    onClick={(e) => {
                                      if (
                                        window.confirm(
                                          "Are you sure to remove this item?"
                                        )
                                      ) {
                                        removeOther(oth.id, oth.amount);
                                      }
                                    }}
                                  >
                                    REMOVE
                                  </span>
                                </div>
                                <div className="sec-rg">
                                  <div className="amnt-sec">
                                    <div className="sec-label">
                                      Total Charge
                                    </div>
                                    <div className="sec-value amnt">
                                      {Pix.dollar(oth.amount, 1)}
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        );
                      })}
                    </div>
                    <div className="ordr-chk-box">
                      <label className="chk-label">
                        <input
                          type="checkbox"
                          name="mntlyDonation"
                          checked={mntlyDonation}
                          onChange={(e) => {
                            const checked = e.target.checked;
                            setMntlyDonation(checked);
                            setTotalAmount((prev) => prev + (checked ? 3 : -3));
                          }}
                        />
                        I want to add $3.00 donation to help offset processing
                        fees, so 100% of my contribution can benefit the
                        organization
                      </label>

                      <div
                        style={{
                          marginTop: "30px",
                          color: "#777",
                        }}
                      >
                        Annual Per Capita. Fully paid Life and Legacy Life
                        members shall pay a twenty-five dollar ($25.00) per
                        capita fee annually. New Life and Legacy Life members
                        shall pay the yearly per capita fee one (1) year after
                        full payment
                      </div>
                    </div>
                    <div className="order-ttl-charge">
                      <div className="ttl-left">
                        <span className="lf-lbl">Total Amount</span>
                        <span className="lf-amnt">
                          {Pix.dollar(totalAmount, 1)}
                        </span>
                      </div>
                      <div className="ttl-right">
                        <Select
                          id="payMethod"
                          placeholder="Choose a Payment option"
                          options={paymentOptions}
                          isSearchable={false}
                          value={paymentOptions.find(opt => opt.value === payMethod)}
                          onChange={(option) => setPayMethod(option.value)}
                        />
                        &nbsp;&nbsp;
                        <button
                          className="btn button"
                          onClick={(e) => makePayment(e)}
                        >
                          Make Payment
                        </button>
                      </div>
                    </div>
                  </>
                )}
              </div>
            </>
          )}

          {showForm && (
            <>
              <h4 className="mb-15">Choose Membership</h4>
              <form>
                <div className="mbrship-sec">
                  <div className="mbrs-col">
                    <div id="ownMembership">
                      <div className="form-row">
                        <div className="form-col sugg">
                          <label htmlFor="section" className="fs-16">
                            Section
                          </label>
                          <Select
                            id="section"
                            name="section"
                            placeholder="Select Section"
                            options={sectionsDropdown}
                            value={
                              sectionsDropdown.find(
                                (option) => option.value === membData.section
                              ) || null
                            }
                            onChange={(selectedOption) => {
                              setMembData({
                                ...membData,
                                section: selectedOption.value,
                                sectionLabel: selectedOption.label,
                              });
                            }}
                          />
                          <Error field="section" />
                        </div>
                        <div className="form-col sugg">
                          <label htmlFor="affiliation" className="fs-16">
                            Affiliation
                          </label>
                          <Select
                            id="affiliation"
                            name="affiliation"
                            placeholder="Select Affiliation"
                            options={affiliatesDropdown}
                            isMulti
                            value={
                              affiliatesDropdown.filter((option) =>
                                membData.affiliate?.includes(option.value)
                              ) || []
                            }
                            onChange={(selectedOptions) => {
                              const selectedValues = selectedOptions
                                ? selectedOptions.map((opt) => opt.value)
                                : [];
                              const selectedLabels = selectedOptions
                                ? selectedOptions.map((opt) => opt.label)
                                : [];

                              setMembData({
                                ...membData,
                                affiliate: selectedValues,
                                affiliateLabel: selectedLabels,
                              });
                            }}
                          />

                          <Error field="affiliation" />
                        </div>
                      </div>
                      <div className="form-row">
                        <div className="form-col">
                          <label htmlFor="" className="fs-16">
                            Choose Membership
                          </label>
                          <Select
                            id="plan"
                            placeholder="Select membership plan"
                            options={dropdown || []}
                            onChange={(selectedOp) => {
                              if (selectedOp && selectedOp.membershipPlanId) {
                                Spn.Show();

                                setMembData({
                                  ...membData,
                                  membershipPlan: selectedOp.membershipPlanId,
                                  membershipPlanName:
                                    selectedOp.membershipPlanName,
                                  membershipPlanCharge:
                                    selectedOp.membershipPlanCharge,
                                  installment: 1,
                                  installmentName: "One-Time Payment",
                                });

                                getInstallments(selectedOp.membershipPlanId)
                                  .then((res) => {
                                    setSubDrop((prevDrpItems) => ({
                                      ...prevDrpItems,
                                      [lgMbr]: {
                                        items: res.data || [],
                                      },
                                    }));
                                  })
                                  .catch((err) => {})
                                  .finally(() => {
                                    Spn.Hide();
                                  });
                              }
                            }}
                            getOptionLabel={(op) => op.membershipPlanName}
                            getOptionValue={(op) => op}
                            value={
                              Array.isArray(dropdown) && membData.membershipPlan
                                ? dropdown.find(
                                    (op) =>
                                      Number(op.membershipPlanId) ===
                                      Number(membData.membershipPlan)
                                  ) || null
                                : null
                            }
                          />
                          <Error field="plan" />
                        </div>
                        <div className="form-col">
                          <label htmlFor="" className="fs-16">
                            Membership For
                          </label>
                          <Select
                            id="membshipFor"
                            placeholder="Select the option"
                            options={
                              //   unique.includes(lgMbr)
                              //     ? MEMBERSHIP_FOR.filter(
                              //         (op) => op.value !== "myself"
                              //       )
                              //     : MEMBERSHIP_FOR
                              MEMBERSHIP_FOR
                            }
                            value={
                              MEMBERSHIP_FOR.find(
                                (op) =>
                                  op.value === (membData.membshipFor || "gift")
                              ) || null
                            }
                            onChange={(selectedOp) => {
                              if (selectedOp.value === "gift") {
                                setIsGift(true);
                                setMembId([]);
                                setContent([]);
                              } else {
                                setMembId({ [lgMbr]: ["myself", ""] });
                                setIsGift(false);
                              }

                              setMembData({
                                ...membData,
                                membshipFor: selectedOp.value,
                              });
                            }}
                          />

                          <Error field="membshipFor" />
                        </div>
                      </div>
                      <div className="form-row">
                        {subDropItems &&
                          subDropItems[lgMbr] &&
                          subDropItems[lgMbr].items &&
                          subDropItems[lgMbr].items.length > 1 && (
                            <div className="form-col">
                              <label htmlFor="" className="fs-16">
                                Installment
                              </label>
                              <Select
                                id="installment"
                                placeholder="Select Installment"
                                options={subDropItems[lgMbr].items || []}
                                value={
                                  subDropItems[lgMbr]?.items.find(
                                    (opt) =>
                                      opt.installment === membData.installment
                                  ) || null
                                }
                                onChange={(selectedOp) => {
                                  setMembData({
                                    ...membData,
                                    installment: selectedOp.installment,
                                    installmentName: selectedOp.title,
                                  });
                                }}
                                getOptionLabel={(op) => op.title}
                                getOptionValue={(op) => op.installment}
                              />
                            </div>
                          )}
                        <div className="form-col add-btn">
                          {isGift && (
                            <>
                              <span
                                className="btn button plr-20 ptb-10"
                                onClick={(e) => {
                                  setMbrOpen(true);
                                }}
                              >
                                <span className="material-symbols-outlined icn">
                                  add_circle
                                </span>
                                <span className="btn-txt">
                                  Add Gift Recipient
                                </span>
                              </span>
                              <Error field="memberGift" />
                            </>
                          )}
                          <div
                            className="gift-membership"
                            style={{ display: isGift ? "block" : "none" }}
                          >
                            <div id="selectedMembers">
                              {content && content.length > 0
                                ? content.map((item) => (
                                    <div
                                      className="ech-mbr"
                                      key={item.id}
                                      id={`person-${item.id}`}
                                    >
                                      <div className="info-sec">
                                        <div className="person-info">
                                          <div className="avatar-sec">
                                            {item.avatarUrl ? (
                                              <div className="mbr-img">
                                                <img
                                                  src={item.avatarUrl}
                                                  alt=""
                                                />
                                              </div>
                                            ) : (
                                              <div className="no-img">
                                                <span className="material-symbols-outlined icn">
                                                  person
                                                </span>
                                              </div>
                                            )}
                                          </div>
                                          <div className="mbr-nam">
                                            {item.name}
                                          </div>
                                        </div>
                                        <div className="action">
                                          <span
                                            className="material-symbols-outlined"
                                            onClick={(e) => {
                                              if (
                                                window.confirm(
                                                  "Are you sure you want to remove this member?"
                                                )
                                              ) {
                                                removeMbr(item.id);
                                              }
                                            }}
                                          >
                                            cancel
                                          </span>
                                        </div>
                                      </div>
                                      <div className="usr-wrap">
                                        <div className="wp-lf">Membership:</div>
                                        <div className="wp-rg">
                                          {item.membership ?? "--"}
                                        </div>
                                      </div>
                                      <div className="usr-wrap">
                                        <div className="wp-lf">Member ID:</div>
                                        <div className="wp-rg">
                                          {item.memberId ?? "--"}
                                        </div>
                                      </div>
                                      <div className="usr-wrap">
                                        <div className="wp-lf">City:</div>
                                        <div className="wp-rg">
                                          {item.city ?? "--"}
                                        </div>
                                      </div>
                                      <div className="usr-wrap">
                                        <div className="wp-lf">Zipcode:</div>
                                        <div className="wp-rg">
                                          {item.zipcode ?? "--"}
                                        </div>
                                      </div>
                                    </div>
                                  ))
                                : ""}
                            </div>
                          </div>
                        </div>
                      </div>

                      <div className="text-left mt-20">
                        <button
                          className="btn btn-success"
                          type="button"
                          onClick={(e) => {
                            handleMembershipForm(e);
                            if (isEdited !== null && isEdited >= 0) {
                              setTotalAmount(
                                (prevAmount) =>
                                  prevAmount - parseFloat(updtAmnt || 0)
                              );
                              setUpdtAmnt(0);
                            }
                          }}
                        >
                          Save
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </>
          )}
        </div>
      </div>
      {isMbrOpen && (
        <SelectMember
          isOpen={isMbrOpen}
          toggle={() => {
            setMbrOpen(!isMbrOpen);
          }}
          addContent={handleAddContent}
          changeURL={props.history.push}
          sectionsDropdown={sectionsDropdown}
          affiliatesDropdown={affiliatesDropdown}
          conditions={{
            section: membData.section,
            affiliate: membData.affiliate,
          }}
        />
      )}
      {openOthPurList && (
        <OtherPurchasableList
          isOpen={openOthPurList}
          toggle={() => {
            setOpenOthPurList(!openOthPurList);
          }}
          purchasableItem={handlePurchasableItem}
          changeURL={props.history.push}
          othPurLists={othPurLists}
        />
      )}
    </Wrapper>
  );
}

export default compose(enhancer, connect(null, { logout }))(Membership);
