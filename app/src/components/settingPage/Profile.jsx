import React, { useState } from "react";
import LabelWithValue from "../../UI/labelWithValue/LabelWithValue";
import Wrapper from "./common.style";
import UpdateProfile from "./EditProfile";
import { store } from "../../redux/store";
import Pix from "../../helper/Pix";
import Checkbox from "../../UI/checkbox/checkbox";
import { RECOMMENDATION_LETTER } from "../../helper/constant";
import BenefitTop from "../../assets/images/Benefit-top.png";
import BenefitBottom from "../../assets/images/Benefit-bottom.png";
import html2canvas from "html2canvas";
import { PROFILE_OPTIONS } from "../../helper/constant";

const Profile = (props) => {
  const isProfileCreated = store.getState().auth.isProfileCreated;
  const accessTkn = store.getState().auth.accessToken;
  const profile = props.data;
  const [editMode, setEditMode] = useState(isProfileCreated ? false : true);
  //   console.log(profile);

  const { userRoles } = store.getState().auth;
  const roleLabels = PROFILE_OPTIONS.memberRole
    .filter((option) => userRoles.includes(option.value))
    .map((option) => option.label);

  const getMembershipCard = () => {
    let container;
    container = document.getElementById("membershipCard");

    html2canvas(container).then(function (canvas) {
      let link = document.createElement("a");
      document.body.appendChild(link);
      link.download = "Membership_card.png";
      link.href = canvas.toDataURL("image/png");
      link.target = "_blank";
      link.click();
      link.parentNode.removeChild(link);
    });
  };

  console.log(profile);

  document.title = "Profile - " + window.seoTagLine;

  return (
    <Wrapper>
      <section className={props.isMobile ? " border plr-15 ptb-30" : ""}>
        <div className="mb-20">
          <h3 className="text-bold">
            Personal Information
            <span
              className="float-right cursor-pointer fs-18"
              onClick={(e) => setEditMode(!editMode)}
            >
              Edit
            </span>
          </h3>
        </div>
        {isProfileCreated ? (
          <>
            <div className="row prf-row">
              <div className="col-6 pt-2">
                <div className="form-group">
                  <label className="fs-18 medium-text">Name&nbsp;:&nbsp;</label>
                  {`
                                        ${profile.profile.prefix.name &&
                      profile.visible.prefix
                      ? profile.profile.prefix.name
                      : ""
                    }
                                        ${profile.profile.firstName || ""}
                                        ${profile.profile.middleName || ""} 
                                        ${profile.profile.lastName || ""}
                                        ${profile.profile.suffix.name
                      ? ", " +
                      (profile.profile.suffix.name ||
                        "")
                      : ""
                    }
                                    `.trim() || "-"}
                </div>
              </div>
              {profile.profile.ageRange ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Age Category&nbsp;:&nbsp;
                    </label>
                    {profile.profile.ageRange}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.racialIdentity.name &&
                profile.visible.racialIdentity ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Racial Identity&nbsp;:&nbsp;
                    </label>
                    {profile.profile.racialIdentity.name ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.household.name && profile.visible.household ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Household&nbsp;:&nbsp;
                    </label>
                    {profile.profile.household.name ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.biography && profile.visible.biography ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Shot Bio&nbsp;:&nbsp;
                    </label>
                    {profile.profile.biography ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              <div className="col-12 pt-2">
                <div className="form-group">
                  <h3 className="text-bold">Contact Information</h3>
                </div>
              </div>
            </div>
            <div className="row prf-row">
              {profile.profile.phoneNumber && profile.visible.phoneNumber ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Mobile&nbsp;:&nbsp;
                    </label>
                    {profile.profile.phoneNumber ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.email && profile.visible.email ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Email&nbsp;:&nbsp;
                    </label>
                    {profile.profile.email ?? "-"}{" "}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.address && profile.visible.address ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Address 1&nbsp;:&nbsp;
                    </label>
                    {profile.profile.address || ""}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.address2 && profile.visible.address2 ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Address 2&nbsp;:&nbsp;
                    </label>
                    {profile.profile.address2 || ""}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.city && profile.visible.city ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      City&nbsp;:&nbsp;
                    </label>
                    {profile.profile.city || ""}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.state.name && profile.visible.state ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      State&nbsp;:&nbsp;
                    </label>
                    {profile.profile.state.name || ""}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.country.name && profile.visible.country ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Country&nbsp;:&nbsp;
                    </label>
                    {profile.profile.country.name || ""}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.zipcode && profile.visible.zipcode ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Zip Code&nbsp;:&nbsp;
                    </label>
                    {profile.profile.zipcode || ""}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.regVotWrdDist &&
                profile.visible.regVotWrdDist ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Registered Voting Precinct / Ward / District&nbsp;:&nbsp;
                    </label>
                    {profile.profile.regVotWrdDist ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {(profile.profile.employerName && profile.visible.employerName) ||
                (profile.profile.businessEmail &&
                  profile.visible.businessEmail) ||
                (profile.profile.employmentStatus.name &&
                  profile.visible.employmentStatus) ||
                (profile.profile.occupation.name && profile.visible.occupation) ||
                (profile.profile.salaryRange.name &&
                  profile.visible.salaryRange) ||
                (profile.profile.industry.name && profile.visible.industry) ||
                (profile.profile.educations.length > 0 &&
                  profile.visible.educations) ||
                (profile.profile.certifications.length > 0 &&
                  profile.visible.certification) ||
                (profile.profile.expertises.length > 0 &&
                  profile.visible.expertise) ||
                (profile.profile.volunteers.length > 0 &&
                  profile.visible.volunteerInterest) ? (
                <div className="col-12 pt-2">
                  <div className="form-group">
                    <h3 className="text-bold">Professional Information</h3>
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.employerName && profile.visible.employerName ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Employer Name&nbsp;:&nbsp;
                    </label>
                    {profile.profile.employerName || ""}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.businessEmail &&
                profile.visible.businessEmail ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Business Email Address&nbsp;:&nbsp;
                    </label>
                    {profile.profile.businessEmail || ""}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.employmentStatus.name &&
                profile.visible.employmentStatus ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Employment Status&nbsp;:&nbsp;
                    </label>
                    {profile.profile.employmentStatus.name || ""}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.occupation.name && profile.visible.occupation ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Occupation&nbsp;:&nbsp;
                    </label>
                    {profile.profile.occupation.name ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.salaryRange.name &&
                profile.visible.salaryRange ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Salary Range&nbsp;:&nbsp;
                    </label>
                    {profile.profile.salaryRange.name || ""}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.industry.name && profile.visible.industry ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Industry&nbsp;:&nbsp;
                    </label>
                    {profile.profile.industry.name ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.educations.length > 0 &&
                profile.visible.educations ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Education&nbsp;:&nbsp;
                    </label>
                    <ul>
                      {profile.profile.educations &&
                        profile.profile.educations.map((edu, index) => {
                          return (
                            <li key={index}>
                              <LabelWithValue
                                label="University"
                                value={edu.university.name}
                              />
                              <LabelWithValue
                                label="Degree"
                                value={edu.degree.name}
                              />
                            </li>
                          );
                        })}
                    </ul>
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.certifications.length > 0 &&
                profile.visible.certification ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Certifications&nbsp;:&nbsp;
                    </label>
                    <ul>
                      {profile.profile.certifications &&
                        profile.profile.certifications.map((cert, index) => {
                          return <li key={index}>{cert.name}</li>;
                        })}
                    </ul>
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.expertises.length > 0 &&
                profile.visible.expertise ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Expertise&nbsp;:&nbsp;
                    </label>
                    <ul>
                      {profile.profile.expertises &&
                        profile.profile.expertises.map((cert, index) => {
                          return <li key={index}>{cert.name}</li>;
                        })}
                    </ul>
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.volunteers.length > 0 &&
                profile.visible.volunteerInterest ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Volunteer Interests&nbsp;:&nbsp;
                    </label>
                    <ul>
                      {profile.profile.volunteers &&
                        profile.profile.volunteers.map((cert, index) => {
                          return <li key={index}>{cert.name}</li>;
                        })}
                    </ul>
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {(profile.profile.gpFirstName && profile.visible.gpFirstName) ||
                (profile.profile.gpLastName && profile.visible.gpLastName) ||
                (profile.profile.gpPhone && profile.visible.gpPhone) ||
                (profile.profile.gpEmail && profile.visible.gpEmail) ? (
                <div className="col-12 pt-2">
                  <div className="form-group">
                    <h3 className="text-bold">
                      Guardian / Parental information
                    </h3>
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.gpFirstName && profile.visible.gpFirstName ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      First Name&nbsp;:&nbsp;
                    </label>
                    {profile.profile.gpFirstName ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.gpLastName && profile.visible.gpLastName ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Last Name&nbsp;:&nbsp;
                    </label>
                    {profile.profile.gpLastName ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.gpPhone && profile.visible.gpPhone ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Phone Number&nbsp;:&nbsp;
                    </label>
                    {profile.profile.gpPhone ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.gpEmail && profile.visible.gpEmail ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Email&nbsp;:&nbsp;
                    </label>
                    {profile.profile.gpEmail ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {(profile.profile.currentChapter.name &&
                profile.visible.currentChapter) ||
                (profile.profile.chapterOfInitiation.name &&
                  profile.visible.chapterOfInitiation) ||
                (profile.profile.yearOfInitiation &&
                  profile.visible.yearOfInitiation) ||
                profile.profile.collegiateSection.name ||
                (profile.profile.affilateOrgzn.length > 0 &&
                  profile.visible.affilateOrgzn) ? (
                <div className="col-12 pt-2">
                  <div className="form-group">
                    <h3 className="text-bold">Organizational Information</h3>
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.organizationalState.name &&
                profile.visible.organizationalState ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      State Coalition&nbsp;:&nbsp;
                    </label>
                    {profile.profile.organizationalState &&
                      profile.profile.organizationalState.name != ""
                      ? profile.profile.organizationalState.name
                      : "-"}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.currentChapter.name &&
                profile.visible.currentChapter ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Current Section&nbsp;:&nbsp;
                    </label>
                    {profile.profile.currentChapter &&
                      profile.profile.currentChapter.name != ""
                      ? profile.profile.currentChapter.name
                      : "National Member"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.chapterOfInitiation.name &&
                profile.visible.chapterOfInitiation ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Section of Origin&nbsp;:&nbsp;
                    </label>
                    {(profile.profile.chapterOfInitiation &&
                      profile.profile.chapterOfInitiation.name) ??
                      "-"}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.collegiateSection.name ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Collegiate Section&nbsp;:&nbsp;
                    </label>
                    {profile.profile.collegiateSection.name ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.affilateOrgzn.length > 0 &&
                profile.visible.affilateOrgzn ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Affiliate Organization&nbsp;:&nbsp;
                    </label>
                    <ul>
                      {profile.profile.affilateOrgzn &&
                        profile.profile.affilateOrgzn.map((aff, index) => {
                          return <li key={index}>{aff.name}</li>;
                        })}
                    </ul>
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.yearOfInitiation &&
                profile.visible.yearOfInitiation ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      First Section Join Date&nbsp;:&nbsp;
                    </label>
                    {profile.profile.yearOfInitiation ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              <div className="col-12 pt-2">
                <div className="form-group">
                  <h3 className="text-bold">Membership & Status Info</h3>
                </div>
              </div>
            </div>
            <div className="row prf-row">
              {profile.profile.memberCode ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Member ID&nbsp;:&nbsp;
                    </label>
                    {profile.profile.memberCode ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {profile.profile.mmbrshpCategory ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Membership Category&nbsp;:&nbsp;
                    </label>
                    {profile.profile.mmbrshpCategory ?? "-"}
                  </div>
                </div>
              ) : (
                profile.profile.isSupporter ? (
                  <div className="col-6 pt-2">
                    <div className="form-group">
                      <label className="fs-18 medium-text">
                        Type&nbsp;:&nbsp;
                      </label>
                      {profile.profile.supportType ?? "-"}
                    </div>
                  </div>
                ) : ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.mmbrshpStatus ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Status&nbsp;:&nbsp;
                    </label>
                    {profile.profile.isSupporter ? (
                      <span className={`sts ${profile.profile.supportSts}`}>
                        {profile.profile.supportSts ?? "-"}
                      </span>
                    ) : (
                      <span className={`sts ${profile.profile.mmbrshpStatus}`}>
                        {profile.profile.mmbrshpStatus ?? "-"}
                      </span>
                    )}
                  </div>
                </div>
              ) : (
                ""
              )}{" "}
              {
                // pending
                profile.profile.yearOfInitiation ? (
                  <div className="col-6 pt-2">
                    <div className="form-group">
                      <label className="fs-18 medium-text">
                        Most Current Paid Date&nbsp;:&nbsp;
                      </label>
                      {profile.profile.yearOfInitiation ?? "-"}
                    </div>
                  </div>
                ) : (
                  ""
                )
              }
            </div>
            <div className="row prf-row">
              {
                // pending
                profile.profile.yearOfInitiation ? (
                  <div className="col-6 pt-2">
                    <div className="form-group">
                      <label className="fs-18 medium-text">
                        Last Paid Date&nbsp;:&nbsp;
                      </label>
                      {profile.profile.yearOfInitiation ?? "-"}
                    </div>
                  </div>
                ) : (
                  ""
                )
              }{" "}
              {profile.profile.joinedDate ? (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <label className="fs-18 medium-text">
                      Join Date&nbsp;:&nbsp;
                    </label>
                    {profile.profile.joinedDate ?? "-"}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              <div className="col-12 pt-2">
                <div className="form-group">
                  <div className="fs-18 medium-text">{profile.profile.isSupporter ? 'Supporter Card' : 'Membership Card'} </div>
                  <div className="mb-wrap">
                    <div className="mb-item">
                      <div className="mb-card" id="membershipCard">
                        <div className="mb-top">
                          <img src={BenefitTop} className="mb-top-img" />
                        </div>
                        <div className="mb-details">
                          <div className="dtl-top">
                            <div className="dtl-itms">
                              <span>Name:</span>
                              <span>
                                {profile.profile.firstName + " " || ""}
                                {profile.profile.middleName + " " || ""}
                                {profile.profile.lastName || ""}
                              </span>
                            </div>
                            <div className="dtl-itms">
                              <span>{profile.profile.isSupporter ? 'Type' : 'Membership'}:</span>
                              <span>
                                {profile.profile.isSupporter ? (profile.profile.supportType ?? "-") : (profile.profile.mmbrshpCategory ?? "-")}
                              </span>
                            </div>
                            <div className="dtl-itms">
                              <span>Member ID:</span>
                              <span>{profile.profile.memberCode ?? "-"}</span>
                            </div>
                            <div className="dtl-itms">
                              <span>Expiration Date:</span>
                              <span>
                                {profile.profile.isSupporter ? (profile.profile.supportEnd ?? "-") : (profile.profile.mmbrshpExpiry ?? "-")}
                              </span>
                            </div>
                          </div>
                          <div className="dtl-btm">
                            Rev. Shavon L. Arline-Bradley, NCNW President & CEO
                          </div>
                        </div>
                        <div className="mb-btm">
                          <img src={BenefitBottom} className="mb-btm-img" />
                        </div>
                      </div>
                    </div>
                    <div className="mb-dwnld">
                      <div className="dwnld-box">
                        <span className="dwnld-btn" onClick={getMembershipCard}>
                          Membership Card
                        </span>
                        <span className="material-symbols-outlined">
                          download
                        </span>
                      </div>
                      <div className="dwnld-box">
                        <a
                          className="dwnld-btn"
                          href={
                            RECOMMENDATION_LETTER +
                            "&token=" +
                            (accessTkn || "")
                          }
                          target="_blank"
                          rel="noreferrer noopener"
                        >
                          Recommendation Letter
                        </a>
                        <span className="material-symbols-outlined">
                          download
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div className="row prf-row">
              {profile.profile.role && profile.profile.role.length > 0 ? (
                <div className="col-12 pt-20">
                  <div className="form-group">
                    <h3 className="text-bold">Elected/Appointed Officer Positions</h3>
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              {profile.profile.role && profile.profile.role.length > 0 ? (
                <div className="col-12 pt-2">
                  <div className="form-group">
                    {/* <label className="fs-18 medium-text">
                      Appointed NCNW Officer Positions&nbsp;:&nbsp;
                    </label> */}
                    <ul>
                      {profile.profile.role.map((rl, index) => (
                        <React.Fragment key={index}>
                          <li>{rl.role + " (" + rl.circle + ")"}</li>
                        </React.Fragment>
                      ))}
                    </ul>
                    {""}
                  </div>
                </div>
              ) : (
                ""
              )}
            </div>
            <div className="row prf-row">
              <div className="col-12 pt-2">
                <div className="form-group">
                  <h3 className="text-bold">Others</h3>
                </div>
              </div>
            </div>
            <div className="row prf-row">
              {profile.profile.deceased == "Y" && (
                <div className="col-6 pt-2">
                  <div className="form-group">
                    <Checkbox
                      label="Deceased"
                      checked={profile.profile.deceased}
                      isDisabled={true}
                    />
                  </div>
                </div>
              )}

              {/* <div className="col-6 pt-2">
                <div className="form-group">
                  <label className="fs-18 medium-text">
                    Total Sum of Legacy Payment&nbsp;:&nbsp;
                  </label>
                  {Pix.dollar(2700, 1)}
                </div>
              </div> */}
            </div>
            <div className="row prf-row">
              <div className="col-6 pt-2">
                <div className="form-group">
                  <label className="fs-18 medium-text">Legacy Membership</label>
                  {profile.profile.legacyTracker ? (
                    <>
                      {/* <div className="pt-2">
                        <span className="font-medium">Payment Status:</span>{" "}
                        {profile.profile.legacyTracker.payStatus}
                      </div> */}
                      <div className="pt-2">
                        <span className="font-medium">Paid:</span> $
                        {profile.profile.legacyTracker.paid}
                      </div>
                      <div className="pt-2">
                        <span className="font-medium">Pin Status:</span>{" "}
                        {profile.profile.legacyTracker.pinStatus ??
                          "Not Eligible"}
                      </div>
                    </>
                  ) : (
                    <p className="text-gray-500">Not enrolled</p>
                  )}
                </div>
              </div>
              {/* <div className="col-6 pt-2">
                <div className="form-group">
                  <label className="fs-18 medium-text">
                    Pin Shipping Indicator&nbsp;:&nbsp;
                  </label>
                  Shipped
                </div>
              </div> */}
            </div>
            <div className="row prf-row">
              <div className="col-6 pt-2">
                <div className="form-group">
                  <label className="fs-18 medium-text">Life Membership</label>
                  {profile.profile.lifeTracker ? (
                    <>
                      {/* <div className="pt-2">
                        <span className="font-medium">Payment Status:</span>{" "}
                        {profile.profile.lifeTracker.payStatus}
                      </div> */}
                      <div className="pt-2">
                        <span className="font-medium">Paid:</span> $
                        {profile.profile.lifeTracker.paid}
                      </div>
                      <div className="pt-2">
                        <span className="font-medium">Pin Status:</span>{" "}
                        {profile.profile.lifeTracker.pinStatus ??
                          "Not Eligible"}
                      </div>
                    </>
                  ) : (
                    <p className="text-gray-500">Not enrolled</p>
                  )}
                </div>
              </div>
              {/* <div className="col-6 pt-2">
                <div className="form-group">
                  <label className="fs-18 medium-text">
                    President Circle&nbsp;:&nbsp;
                  </label>
                  Sample Circle
                </div>
              </div> */}
            </div>
          </>
        ) : (
          <React.Fragment>
            <LabelWithValue
              label="Name"
              value={profile.profile.firstName + " " + profile.profile.lastName}
            />
            <LabelWithValue label="Email" value={profile.profile.email} />
            <LabelWithValue
              label="Member ID"
              value={profile.profile.memberCode}
            />
          </React.Fragment>
        )}
        {editMode && (
          <UpdateProfile
            show={editMode}
            toggle={() => setEditMode(!editMode)}
            profile={profile}
            updatePage={() => window.location.reload()}
          />
        )}
      </section>
    </Wrapper>
  );
};

export default Profile;
