import React, { useEffect, useState } from "react";
import { Modal } from "reactstrap";
import Wrapper from "./dues.style";
import { getAllMembers, duesNewMember, getDropdown } from "../../api/duesAPI";
import { getSubordinates } from "../../api/LeadershipAPI";
import Spinner from "../../UI/Spinner/Spinner";
import Input from "../../UI/input/input";
import Select from "../../UI/select/select";
import Toast from "../../UI/Toast/Toast";
import MultiSelect from "react-multi-select-component";
import { PROFILE_OPTIONS } from "../../helper/constant";

const WIDTH_CLASS = window.innerWidth >= 1024 ? "wp-80" : "wp-100";

function SelectMember(props) {
  const [search, setSearch] = useState("");
  const [mbrData, setMbrData] = useState(null);
  const [popupView, setPopupView] = useState(false);
  const [country, setCountryList] = useState([]);
  const [states, setStateList] = useState([]);
  const [filteredStates, setFilteredStates] = useState([]);
  const [selectedOption, setSelectedOption] = useState("exist");
  const [ErrorList, setErrorList] = useState({});
  const [sectionList, setSectionList] = useState([]);
  const [affiliationList, setAffiliationList] = useState([]);
  const [formValues, setFormValues] = useState({
    firstName: "",
    lastName: "",
    email: "",
    address: "",
    city: "",
    zipcode: "",
    phone: "",
    section: "",
    affilation: [],
  });

  let Spn = Spinner();
  const Tst = Toast();
  let pgn = 1;

  const getQueryParams = () => {
    return {
      pgn: pgn,
      search: search,
      ...(props.conditions || {}),
    };
  };

  useEffect(() => {
    Spn.Show();
    const queryParams = getQueryParams();
    if (props.sectionsDropdown) {
      setSectionList(props.sectionsDropdown);
    }
    if (props.affiliatesDropdown) {
      setAffiliationList(props.affiliatesDropdown);
    }
    getDropdown({
      nations: "all",
      states: "all",
      sections: !props.sectionsDropdown ? "all" : false,
      affiliations: !props.affiliatesDropdown ? "all" : false,
    })
      .then((res) => {
        if (!props.sectionsDropdown) {
          setSectionList(res.data.sections || []);
        }
        if (!props.affiliatesDropdown) {
          setAffiliationList(res.data.affiliations || []);
        }
        setCountryList(res.data.nations || []);
        setStateList(res.data.states || []);
        setFilteredStates(res.data.states || []);
      })
      .catch((err) => {
        Tst.Error("Failed to retrieve dropdown data. Please try again later!");
      });
    if (props.formType === "elect-officer") {
      getSubordinates(pgn, "")
        .then((res) => {
          if (res.success === 1) {
            setMbrData(res.data);
          } else {
          }
          Spn.Hide();
        })
        .catch((err) => { });
    } else {
      getAllMembers(queryParams)
        .then((res) => {
          if (res.success === 1) {
            setMbrData(res.data);
          } else {
          }
          Spn.Hide();
        })
        .catch((err) => { });
    }
  }, []);
  useEffect(() => {
    const relevant = states.filter((st) => st.nation == formValues.country);
    setFilteredStates(relevant);
  }, [formValues.country]);

  const storeData = (e) => {
    setFormValues({
      ...formValues,
      [e.target.name]: e.target.value,
    });
  };
  const showMembers = (e, flg = false) => {
    Spn.Show();
    const queryParams = getQueryParams();
    if (props.formType === "elect-officer") {
      getSubordinates(pgn, document.getElementById("srchKey").value)
        .then((res) => {
          if (res.success === 1) {
            if (flg == true) {
              setMbrData((prevData) => ({
                ...prevData,
                list: [...prevData.list, ...res.data.list],
                currentPageNo: res.data.currentPageNo,
                totalPages: res.data.totalPages,
              }));
            } else {
              setMbrData(res.data);
            }
          } else {
          }
        })
        .catch((err) => {
          //
        })
        .finally(() => {
          Spn.Hide();
        });
    } else {
      getAllMembers(queryParams)
        .then((res) => {
          if (res.success === 1) {
            if (flg == true) {
              setMbrData((prevData) => ({
                ...prevData,
                list: [...prevData.list, ...res.data.list],
                currentPageNo: res.data.currentPageNo,
                totalPages: res.data.totalPages,
              }));
            } else {
              setMbrData(res.data);
            }
          } else {
          }
        })
        .catch((err) => {
          //
        })
        .finally(() => {
          Spn.Hide();
        });
    }

    e.preventDefault();
    return false;
  };

  const showMore = (e, cp, apndFlg) => {
    pgn = cp + 1;
    showMembers(e, apndFlg);
  };

  const handleClick = (event) => {
    const personDetails = {
      id: parseInt(event.target.getAttribute("data-id")),
      name: event.target.getAttribute("data-name"),
      avatarUrl: event.target.getAttribute("data-avatar"),
      section: event.target.getAttribute("data-section"),
      affiliation: event.target.getAttribute("data-affiliation"),
      city: event.target.getAttribute("data-city"),
      zipcode: event.target.getAttribute("data-zipcode"),
      memberId: event.target.getAttribute("data-memberid"),
      membership: event.target.getAttribute("data-membership"),
    };
    props.addContent(personDetails);
  };

  const showPopupOptns = (e) => {
    if (e.target.value == "new") {
      setPopupView(true);
    } else {
      setPopupView(false);
    }
  };

  const Error = ({ field }) => {
    return ErrorList[field] ? (
      <div className="text-danger">{ErrorList[field]}</div>
    ) : (
      <></>
    );
  };

  const handleForm = () => {
    function el(id) {
      return document.getElementById(id);
    }

    let sErrs = {};

    if (!el("prefix").value.trim()) {
      sErrs["prefix"] = "This field is required";
    }
    if (!el("firstName").value.trim()) {
      sErrs["firstName"] = "This field is required";
    }
    if (!el("lastName").value.trim()) {
      sErrs["lastName"] = "This field is required";
    }
    if (!el("email").value.trim()) {
      sErrs["email"] = "This field is required";
    }
    if (!el("country").value.trim()) {
      sErrs["country"] = "This field is required";
    }
    if (!el("state").value.trim()) {
      sErrs["state"] = "This field is required";
    }
    if (!el("city").value.trim()) {
      sErrs["city"] = "This field is required";
    }
    if (!el("address").value.trim()) {
      sErrs["address"] = "This field is required";
    }
    if (!el("zipcode").value.trim()) {
      sErrs["zipcode"] = "This field is required";
    }
    if (!formValues.section) {
      sErrs["section"] = "This field is required";
    }
    if (formValues.affilation.length < 1) {
      sErrs["affilation"] = "This field is required";
    }

    setErrorList(sErrs);

    if (Object.keys(sErrs).length < 1) {
      Spn.Show();

      const data = {
        method: "dues-add-new-member",
        prefix: formValues.prefix,
        firstName: formValues.firstName,
        lastName: formValues.lastName,
        email: formValues.email,
        address: formValues.address,
        city: formValues.city,
        state: formValues.state,
        country: formValues.country,
        zipcode: formValues.zipcode,
        phone: formValues.phone,
        section: formValues.section,
        affilation: formValues.affilation
          ? formValues.affilation.map((aff) => aff.value)
          : [],
      };

      duesNewMember(data)
        .then((res) => {
          if (res.success === 1) {
            const newMemberDetails = {
              id: res.data.id,
              name: res.data.name,
              avatarUrl: res.data.avatarUrl,
              section: res.data.section,
              affiliation: res.data.affiliation,
              city: res.data.city,
              zipcode: res.data.zipcode,
              memberId: res.data.memberId,
              country: res.data.country,
              state: res.data.state,
              prefix: res.data.prefix,
            };
            props.addContent(newMemberDetails);
          } else {
            Tst.Error(res.message);
            sErrs["email"] = res.message;
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

  return (
    <div>
      {Spn.Obj}
      <Modal
        isOpen={props.isOpen}
        toggle={props.toggle}
        centered
        size="lg"
        className="signin"
        backdrop="static"
        keyboard={false}
      >
        <Wrapper>
          <div className="plr-30 ptb-50 position-relative">
            <div className="popup-title">Choose Member</div>
            <div
              className="cursor-pointer text-bold close"
              onClick={(e) => {
                props.toggle();
              }}
            >
              X
            </div>
            <div className="radion-ops">
              <label className="rd-ops">
                <input
                  type="radio"
                  name="giftto"
                  value="exist"
                  checked={selectedOption === "exist"}
                  onChange={(e) => {
                    setSelectedOption(e.target.value);
                    showPopupOptns(e);
                  }}
                />
                Existing
              </label>
              <label className="rd-ops">
                <input
                  type="radio"
                  name="giftto"
                  value="new"
                  checked={selectedOption === "new"}
                  onChange={(e) => {
                    setSelectedOption(e.target.value);
                    showPopupOptns(e);
                  }}
                />
                New
              </label>
            </div>
            {!popupView && (
              <div className="containers">
                <div className="mbr-srch">
                  <div className="srch-bar">
                    <form onSubmit={(e) => showMembers(e)}>
                      <input
                        type="text"
                        name="key"
                        className="key-inp"
                        id="srchKey"
                        onChange={(e) => setSearch(e.target.value)}
                      />
                      <button
                        type="button"
                        className="srch-btn"
                        onClick={(e) => showMembers(e)}
                      >
                        <span className="material-symbols-outlined">
                          search
                        </span>
                      </button>
                    </form>
                  </div>
                  {mbrData && mbrData.totalPages ? (
                    mbrData.list && mbrData.list.length > 0 ? (
                      <>
                        {mbrData.list.map((mbr) => {
                          return (
                            <div className="each-mbr" key={mbr.id}>
                              <div className="avatar-sec">
                                {mbr.avatar ? (
                                  <div className="mbr-img">
                                    <img src={mbr.avatar} alt="" />
                                  </div>
                                ) : (
                                  <div className="no-img">
                                    <span className="material-symbols-outlined icn">
                                      person
                                    </span>
                                  </div>
                                )}
                              </div>
                              <div className="nam-sec">
                                <div className="name">{mbr.name}</div>
                                <div className="memberId">{`ID : ${mbr.memberId}`}</div>
                                <div className="address">
                                  {/* {`${[mbr.city, mbr.stateName]
                                    .filter((v) => v)
                                    .join(", ")}`} */}
                                  {[mbr.secName, mbr.collegiateSection]
                                    .find((v) => v && v.trim())
                                  }
                                </div>
                              </div>
                              <div className="actn">
                                <span
                                  className="btn add-btn"
                                  onClick={handleClick}
                                  data-id={mbr.id}
                                  data-name={mbr.name}
                                  data-avatar={mbr.avatar}
                                  data-section={mbr.secName}
                                  data-affiliation={mbr.affName}
                                  data-city={mbr.city}
                                  data-zipcode={mbr.zipcode}
                                  data-memberid={mbr.memberId}
                                  data-membership={mbr.membership}
                                >
                                  Add
                                </span>
                              </div>
                            </div>
                          );
                        })}
                        {mbrData.totalPages &&
                          mbrData.currentPageNo &&
                          mbrData.totalPages > mbrData.currentPageNo ? (
                          <div className="show-more">
                            <span
                              className="btn"
                              onClick={(e) =>
                                showMore(e, mbrData.currentPageNo, true)
                              }
                            >
                              Show more
                            </span>
                          </div>
                        ) : (
                          ""
                        )}
                      </>
                    ) : (
                      ""
                    )
                  ) : (
                    <div className="text-center">No members found!</div>
                  )}
                </div>
              </div>
            )}
            {popupView && (
              <form action="">
                <div className="mb-15 prefix">
                  <Select
                    label="Prefix"
                    name="prefix"
                    placeholder="Choose prefix"
                    fontSize={"fs-16 text-dark"}
                    id="prefix"
                    options={PROFILE_OPTIONS.prefix}
                    onChange={storeData}
                    value={formValues.prefix || ""}
                  />
                  <Error field="section" />
                </div>
                <div className="mb-15">
                  <Input
                    id="firstName"
                    name="firstName"
                    label="First Name"
                    placeholder="First Name"
                    fontSize={"fs-16 text-dark"}
                    contentFontSize="fs-14"
                    type="text"
                    onChange={storeData}
                  />
                  <Error field="firstName" />
                </div>
                <div className="mb-15">
                  <Input
                    id="lastName"
                    name="lastName"
                    label="Last Name"
                    placeholder="Last Name"
                    fontSize={"fs-16 text-dark"}
                    contentFontSize="fs-14"
                    type="text"
                    onChange={storeData}
                  />
                  <Error field="lastName" />
                </div>
                <div className="mb-15">
                  <Input
                    id="email"
                    label="Email"
                    name="email"
                    placeholder="Email"
                    fontSize={"fs-16 text-dark"}
                    contentFontSize="fs-14"
                    type="text"
                    onChange={storeData}
                  />
                  <Error field="email" />
                </div>
                <div className="mb-15 member">
                  <Select
                    label="Section"
                    name="section"
                    placeholder="Choose Section"
                    id="section"
                    fontSize={"fs-16 text-dark"}
                    options={sectionList}
                    onChange={storeData}
                    value={formValues.section}
                  />
                  <Error field="section" />
                </div>
                <div className="mb-15 member">
                  <label className="fs-16 text-dark">Affiliates</label>
                  <MultiSelect
                    id="affilation"
                    options={affiliationList.map((el) => {
                      return {
                        label: el.label,
                        value: el.value,
                      };
                    })}
                    value={
                      formValues.affilation.length > 0
                        ? formValues.affilation
                        : []
                    }
                    onChange={(value) => {
                      setFormValues((prev) => ({
                        ...prev,
                        affilation: value,
                      }));
                    }}
                  />
                  <Error field="affilation" />
                </div>
                <div className="addr-label">Shipping Address</div>
                <div className="mb-15 country">
                  <Select
                    label="Country"
                    name="country"
                    placeholder="Choose country"
                    id="country"
                    fontSize={"fs-16 text-dark"}
                    options={country || []}
                    onChange={storeData}
                    value={formValues.country || ""}
                  />
                  <Error field="country" />
                </div>
                <div className="mb-15 state">
                  <Select
                    label="State"
                    name="state"
                    placeholder="Choose state"
                    id="state"
                    fontSize={"fs-16 text-dark"}
                    options={filteredStates || []}
                    onChange={storeData}
                    value={formValues.state || ""}
                  />
                  <Error field="state" />
                </div>
                <div className="mb-15">
                  <Input
                    id="address"
                    label="Address"
                    name="address"
                    placeholder="Address"
                    fontSize={"fs-16 text-dark"}
                    contentFontSize="fs-14"
                    type="text"
                    onChange={storeData}
                  />
                  <Error field="address" />
                </div>
                <div className="mb-15">
                  <Input
                    id="city"
                    label="City"
                    name="city"
                    placeholder="City"
                    fontSize={"fs-16 text-dark"}
                    contentFontSize="fs-14"
                    type="text"
                    onChange={storeData}
                  />
                  <Error field="city" />
                </div>
                <div className="mb-15">
                  <Input
                    id="zipcode"
                    label="Zipcode"
                    name="zipcode"
                    placeholder="Zipcode"
                    fontSize={"fs-16 text-dark"}
                    contentFontSize="fs-14"
                    type="text"
                    onChange={storeData}
                  />
                  <Error field="zipcode" />
                </div>
                <div className="mb-15">
                  <Input
                    id="phone"
                    label="Phone"
                    name="phone"
                    placeholder="Phone"
                    fontSize={"fs-16 text-dark"}
                    contentFontSize="fs-14"
                    type="text"
                    onChange={storeData}
                  />
                  <Error field="phone" />
                </div>

                <div className="text-center">
                  <button
                    className="btn btn-rounded button plr-50 ptb-10 mt-20"
                    type="button"
                    onClick={(e) => handleForm(e)}
                  >
                    Save
                  </button>
                </div>
              </form>
            )}
          </div>
        </Wrapper>
      </Modal>
    </div>
  );
}

export default SelectMember;
