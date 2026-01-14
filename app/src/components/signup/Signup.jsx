import React, { useState, useEffect } from "react";
import { compose } from "redux";
import SignUpWrapper from "./signup.style";
import Button from "../../UI/button/button";
import Input from "../../UI/input/input";
import Select from "../../UI/select/select";
import MultiSelect from "react-multi-select-component";
import { withRouter } from "react-router-dom";
// import { Modal } from "reactstrap";
import {
	// SITE_NAME,
	// SITE_SHORT_DESC,
	// WEBSITE_URL,
	REGISTER_TYPE,
} from "../../helper/constant";
// import Logo from "../../assets/images/logo.png";
import enhancer from "./enhancer";
import { Link } from "react-router-dom";
// import FB from "../../assets/images/fb_icon_1x.png";
// import Google from "../../assets/images/google_icon_1x.png";
import {
	signUp as createAccount,
	// logInViaSMedia,
	getSection,
	getAffiliation,
	getCollegiateDropdown,
} from "../../api/commonAPI";

import Toast from "../../UI/Toast/Toast";
import Spinner from "../../UI/Spinner/Spinner";

const loadFacebookSDK = () => {
	(function (d, s, id) {
		var js,
			fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {
			return;
		}
		js = d.createElement(s);
		js.id = id;
		js.src = "https://connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	})(document, "script", "facebook-jssdk");
};

const initializeFacebookSDK = (appId) => {
	window.fbAsyncInit = function () {
		window.FB.init({
			appId: appId,
			cookie: true,
			xfbml: true,
			version: "v11.0",
		});
		window.FB.AppEvents.logPageView();
	};
};

const loadGoogleSDK = () => {
	const script = document.createElement("script");
	script.src = "https://apis.google.com/js/platform.js";
	script.async = true;
	script.defer = true;
	// script.onload = () => {
	//   window.gapi.load('auth2', () => {
	//     window.gapi.auth2.init({
	//       client_id: '',
	//     });
	//   });
	// };
	document.body.appendChild(script);
};

const SignUp = (props) => {
	const [passwordType, setPasswordType] = useState("password");
	const [rePasswordType, setRePasswordType] = useState("password");
	const [sectionList, setSectionList] = useState([]);
	const [affiliationList, setAffiliationList] = useState([]);
	const [collegiateSectionList, setCollegiateSectionList] = useState([]);

	const Tst = Toast();
	const Spn = Spinner();

	const {
		values,
		handleChange,
		handleBlur,
		errors,
		touched,
		submitCount,
		isValid,
	} = props;

	const Error = (props) => {
		const field1 = props.field;
		if ((errors[field1] && touched[field1]) || submitCount > 0) {
			return (
				<span className={props.class ? props.class : "error-msg"}>
					{errors[field1]}
				</span>
			);
		} else {
			return <span />;
		}
	};

	document.title = "Sign Up - " + window.seoTagLine;

	useEffect(() => {
		loadFacebookSDK();
		initializeFacebookSDK("");
		loadGoogleSDK();
		const addNAOption = (data) => [
			...data,
			{ label: "N/A (Not Applicable)", value: "na" },
		];
		getSection()
			.then((res) => setSectionList(addNAOption(res.data)))
			.catch((err) => {
				Tst.Error("Failed to retrive Section list. Please try again later!");
			});
		getAffiliation(0)
			.then((res) => setAffiliationList(addNAOption(res.data)))
			.catch((err) => {
				Tst.Error(
					"Failed to retrive Affiliation list. Please try again later!"
				);
			});
		getCollegiateDropdown(0)
			.then((res) => setCollegiateSectionList(addNAOption(res.data)))
			.catch((err) => {
				Tst.Error("Failed to retrive collegiate list. Please try again later!");
			});
	}, []);

	/* const handleGoogleLogin = () => {
	  const auth2 = window.gapi.auth2.getAuthInstance();
	  auth2
		.signIn()
		.then((googleUser) => {
		  const profile = googleUser.getBasicProfile();
		  const userData = {
			email: profile.getEmail(),
			firstName: profile.getGivenName(),
			lastName: profile.getFamilyName(),
			imageUrl: profile.getImageUrl(),
			googleId: profile.getId(),
		  };
		  handleSMediaSignIn(userData);
		})
		.catch((error) => {
		  console.error("Google login error", error);
		});
	}; */

	/* const handleFacebookLogin = () => {
	  window.FB.login(
		(response) => {
		  if (response.authResponse) {
			window.FB.api(
			  "/me",
			  { fields: "first_name,last_name,email,picture" },
			  (response) => {
				const userData = {
				  email: response.email,
				  firstName: response.first_name,
				  lastName: response.last_name,
				  imageUrl: response.picture.data.url,
				  facebookId: response.id,
				};
				handleSMediaSignIn(userData);
			  }
			);
		  } else {
			console.log("User cancelled login or did not fully authorize.");
		  }
		},
		{ scope: "public_profile,email" }
	  );
	}; */

	/* const Login = () => {
	  return (
		<>
		  {Tst.Obj}
		  {Spn.Obj}
		  <div className="flex-item">
			<Link to="/">
			  <img
				src={Logo}
				alt={SITE_NAME}
				className="image-size"
				width="50px"
				height="50px"
			  />
			  <div>
				<label className="white--text text-bold fs-25 letter-spacing-2 title mt-3 mb-0">
				  {SITE_NAME}
				</label>
			  </div>
			  <p className="white--text text-bold fs-7 short-desc">
				{SITE_SHORT_DESC}
			  </p>
			</Link>
			<h4 className="text-bold mt-20">Welcome Back!</h4>
			<p className="mt-10">Login to access your account</p>
			<Button
			  className="border-radius-41 bg-white mt-20"
			  name="LOGIN"
			  clicked={() => props.history.push("/signin")}
			/>
		  </div>
		</>
	  );
	}; */

	/* const handleSMediaSignIn = (userData) => {
	  Spn.Show();
	  const body = {
		method: "login-via-smedia",
		email: userData.email,
		firstName: userData.firstName,
		lastName: userData.lastName,
		imageUrl: userData.imageUrl,
		facebookId: userData.facebookId || null,
		googleId: userData.googleId || null,
		registerType: userData.googleId
		  ? REGISTER_TYPE.google
		  : userData.facebookId
		  ? REGISTER_TYPE.facebook
		  : REGISTER_TYPE.normal,
		deviceType: "web",
	  };
  
	  logInViaSMedia(body)
		.then((res) => {
		  if (res.success === 1) {
			const userData = {
			  isLogin: true,
			  accessToken: res.data.accessToken,
			  memberId: res.data.memberId,
			  firstName: res.data.firstName,
			  lastName: res.data.lastName,
			  referralPoints: res.data.refferalPoints || 0,
			  prefix: res.data.prefix,
			  profileImage: res.data.profileImage,
			  isProfileCreated: res.data.profileCreated,
			  isNotificationOn: res.data.notification || false,
			  currentChapter: res.data.currentChapter,
			  userRoles: res.data.roles,
			  membershipStatus: res.data.membershipStatus,
			};
			props.login(userData);
			Tst.Success(res.message);
			if (res.data.profileCreated) {
			  props.history.push("/home");
			} else {
			  props.history.push("/account");
			}
		  } else {
			props.resetForm();
			Tst.Error(res.message);
		  }
		})
		.catch((err) => {
		  Tst.Error("Something went wrong!");
		})
		.finally(() => {
		  Spn.Hide();
		});
	}; */

	const handleSignup = (e) => {
		console.log(isValid);
		if (isValid) {
			if (!values.section && !values.affiliation && !values.collegiateSection) {
				console.log(
					"Please select at least one: Section, Affiliation, or Collegiate Section."
				);
				Tst.Error(
					"Please select at least one: Section, Affiliation, or Collegiate Section."
				);
				return;
			}
			Spn.Show();

			const body = {
				method: "signup",
				firstName: values.firstName,
				lastName: values.lastName,
				email: values.email,
				password: values.password,
				section: values.section,
				affiliation: values.affiliation,
				collegiate: values.collegiateSection,
				facebookId: null,
				googleId: null,
				registerType: REGISTER_TYPE.normal,
			};
			createAccount(body)
				.then((res) => {
					if (res.success === 1) {
						props.history.push("/account-created");
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

	return (
		<>
			{Tst.Obj}
			{Spn.Obj}
			<SignUpWrapper>
				<div className="sgp-container">
					<div className="ttl-1">DON'T HAVE AN ACCOUNT?</div>
					<div className="ttl-1-sub">
						<strong>
							If you’re an existing member and this is your first time logging
							in, you’ll need to claim your account.
						</strong>
						To do so, please click “
						<strong>
							<Link to="/signin/forgot">Forgot Password</Link>
						</strong>
						”. You’ll receive a one-time passcode (OTP) at the email address you
						have on file. Once you receive the OTP, return to the login page and
						sign in using that temporary password to claim your account.
					</div>
					<div className="ttl-2">CREATE AN ACCOUNT</div>

					<div className="form-area">
						<div className="form-col">
							<div className="fm-row">
								<Input
									label="FIRST NAME"
									type="text"
									placeholder="FIRST NAME"
									id="firstName"
									onChange={handleChange}
									onBlur={handleBlur}
									value={values.firstName || ""}
								/>
								<Error field="firstName" />
							</div>
							<div className="fm-row">
								<Input
									label="LAST NAME"
									type="text"
									placeholder="LAST NAME"
									id="lastName"
									onChange={handleChange}
									onBlur={handleBlur}
									value={values.lastName || ""}
								/>
								<Error field="lastName" />
							</div>
							<div className="fm-row">
								<Input
									label="EMAIL"
									type="text"
									placeholder="EMAIL"
									id="email"
									onChange={handleChange}
									onBlur={handleBlur}
									value={values.email || ""}
								/>
								<Error field="email" />
							</div>
							<div className="fm-row">
								<Select
									label="SECTION"
									placeholder="CHOOSE SECTION"
									id="section"
									options={sectionList}
									onChange={handleChange}
									onBlur={handleBlur}
									value={values.section || ""}
								/>
							</div>
						</div>
						<div className="form-col">
							<div className="fm-row">
								<label className="insidelabelmain mb-10">AFFILIATION</label>
								<MultiSelect
									id="affiliation"
									options={affiliationList.map((el) => ({
										label: el.label,
										value: el.value,
									}))}
									onChange={(selected) => {
										handleChange({
											target: {
												name: "affiliation",
												value: selected,
											},
										});
									}}
									onBlur={handleBlur}
									value={values.affiliation || []}
									className="inputmain pa-10 multiselect"
								/>
							</div>

							<div className="fm-row">
								<Select
									label="COLLEGIATE SECTION"
									placeholder="CHOOSE COLLEGIATE SECTION"
									id="collegiateSection"
									options={collegiateSectionList}
									onChange={handleChange}
									onBlur={handleBlur}
									value={values.collegiateSection || ""}
								/>
							</div>
							<div className="fm-row">
								<div className="position-relative">
									<Input
										label="PASSWORD"
										type={passwordType}
										placeholder="PASSWORD"
										id="password"
										onChange={handleChange}
										onBlur={handleBlur}
										value={values.password || ""}
									/>
									{passwordType === "password" ? (
										<span
											className="material-symbols-outlined eye pwd cursor-pointer"
											onClick={() => { setPasswordType("text"); }}
										>
											visibility
										</span>
									) : (
										<span
											className="material-symbols-outlined eye pwd cursor-pointer"
											onClick={() => { setPasswordType("password"); }}
										>
											visibility_off
										</span>
									)}
									<Error field="password" />
									<div className="pws-rule">
										Must Contain 8 Characters, One Uppercase, One Lowercase, One
										Number and one special case Character
									</div>
								</div>
							</div>
							<div className="fm-row">
								<div className="position-relative">
								<Input
									label="CONFIRM PASSWORD"
									type={rePasswordType}
									placeholder="PASSWORD"
									id="confirmPwd"
									onChange={handleChange}
									onBlur={handleBlur}
									value={values.confirmPwd || ""}
								/>
								{rePasswordType === "password" ? (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => { setRePasswordType("text"); }}
									>
										visibility
									</span>
								) : (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => { setRePasswordType("password"); }}
									>
										visibility_off
									</span>
								)}
								</div>
								<Error field="confirmPwd" />
							</div>
						</div>
					</div>

					<div className="sgp-agree">
						BY CREATING AN ACCOUNT YOU AGREE TO OUR{" "}
						<Link to="/terms_of_service/">TERMS OF SERVICE</Link>
						{" AND "}
						<Link to="/privacy_policy/">PRIVACY POLICY</Link>
					</div>

					<div className="submit-area">
						<Button
							className="button mt-20"
							name="SIGN UP"
							clicked={handleSignup}
						/>
					</div>

					{/* <div className="sgp-agree">
          OR SIGNUP WITH
        </div>
        <div className="d-flex justify-content-center">
            <span onClick={handleFacebookLogin}>
              <img
                src={FB}
                alt="Create with Facebook"
                className="mr-20"
              />
            </span>
            <span>
              <span onClick={handleGoogleLogin}>
                <img src={Google} alt="Create with Google" className="" />
              </span>
            </span>
          </div> */}
				</div>
			</SignUpWrapper>
		</>
	);
};

export default compose(withRouter, enhancer)(SignUp);
