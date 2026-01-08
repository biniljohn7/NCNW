import React, { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import { connect } from "react-redux";
import { compose } from "redux";
import SignInWrapper from "./signin.style";
import Button from "../../UI/button/button";
import Input from "../../UI/input/input";
import { withRouter } from "react-router-dom";
import { Modal } from "reactstrap";
import {
	SITE_NAME,
	SITE_SHORT_DESC,
	REGISTER_TYPE,
} from "../../helper/constant";
import { LoginEnhancer as enhancer } from "./enhancer";
import AuthActions from "../../redux/auth/actions";
import { Link } from "react-router-dom";
import FB from "../../assets/images/fb_icon_1x.png";
import Google from "../../assets/images/google_icon_1x.png";
import ForgotPassword from "../forgotPassword/ForgotPassword";
import ResetPassword from "../forgotPassword/ResetPassword";
import { login as logIn, emailLoginReq } from "../../api/commonAPI";

import Toast from "../../UI/Toast/Toast";
import Spinner from "../../UI/Spinner/Spinner";

import Logo from "../../assets/images/logo.png";

const { login, logInViaSMedia } = AuthActions;

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

const SignIn = (props) => {
	const { args } = useParams();
	const [signInState, setSignInState] = useState(true);
	const [passwordType, setPasswordType] = useState("password");
	const [setForgotPassword, setForgotPasswordState] = useState(false);
	const [step, setStep] = useState(1);
	const [email, setEmail] = useState("");
	const [password, setPassword] = useState("");
	const [otp, setOtp] = useState("");
	const [emailValid, setEmailValid] = useState(false);
	const [resetPassword, setResetPassword] = useState(false);
	const [lgWithOtp, setLgWithOtp] = useState(false);

	const Tst = Toast();
	const Spn = Spinner();

	useEffect(() => {
		const loadGoogleSDK = () => {
			const script = document.createElement("script");
			script.src = "https://accounts.google.com/gsi/client";
			script.async = true;
			script.defer = true;
			script.onload = () => {
				if (window.google) {
					window.google.accounts.id.initialize({
						client_id:
							"374834160970-r11lok9u1jev7j8pid5fgn1qsenuhct5.apps.googleusercontent.com",
						callback: handleCredentialResponse,
						cancel_on_tap_outside: false,
						ux_mode: "popup",
					});
				} else {
					console.error("Google SDK not loaded");
				}
			};
			document.body.appendChild(script);
		};

		loadFacebookSDK();
		initializeFacebookSDK("453770680596721");
		loadGoogleSDK();
	}, []);
	useEffect(() => {
		if (args == 'forgot') {
			props.resetForm();
			toggleForgotPassword();
		}
	}, [args]);

	const toggleResetPassword = () => {
		setResetPassword(!resetPassword);
	};

	const handleCredentialResponse = (response) => {
		const credential = response.credential;
		const payload = JSON.parse(atob(credential.split(".")[1]));

		const userData = {
			email: payload.email,
			firstName: payload.given_name,
			lastName: payload.family_name,
			imageUrl: payload.picture,
			googleId: payload.sub,
		};

		handleSMediaSignIn(userData);
	};

	const handleSigninFailure = (error) => {
		if (error.error === "popup_closed_by_user") {
			Tst.Error("Sign-in process was not completed. Please try again.");
		} else {
			console.error("Sign-in error: ", error);
			Tst.Error("An error occurred during sign-in. Please try again later.");
		}
	};

	const handleGoogleLogin = () => {
		if (window.google) {
			window.google.accounts.id.prompt((notification) => {
				if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
					handleSigninFailure({ error: "popup_closed_by_user" });
				}
			});
		}
	};

	const handleFacebookLogin = () => {
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
	};

	function toggleForgotPassword() {
		if (setForgotPassword === false) {
			setSignInState(false);
			setForgotPasswordState(!setForgotPassword);
		} else {
			setForgotPasswordState(!setForgotPassword);
			setSignInState(true);
		}
	}

	function reset() {
		setForgotPasswordState(false);
		setResetPassword(false);
		setLgWithOtp(false);
		if (signInState === false) {
			setSignInState(true);
		}
	}

	const { errors, touched, submitCount } = props;

	const Error = (props) => {
		const field1 = props.field;
		if ((errors[field1] && touched[field1]) || submitCount > 0) {
			return (
				<div className={props.class ? props.class : "error-msg"}>
					{errors[field1]}
				</div>
			);
		} else {
			return <div />;
		}
	};

	const toggelModal = () => {
		setSignInState(!signInState);
	};

	document.title = "Sign In - " + window.seoTagLine;

	const handleSMediaSignIn = (userData) => {
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
	};

	const validateEmail = (e) => {
		Spn.Show();

		const body = {
			method: "email-login-request",
			email: email,
		};

		emailLoginReq(body)
			.then((res) => {
				if (res.success === 1) {
					setEmailValid(true);
					setStep(2);
					if (res.lgWithOtp) {
						setLgWithOtp(true);
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
	};

	const handleSignIn = (e) => {
		Spn.Show();

		const body = {
			method: "login",
			email: email,
			password: password,
			facebookId: null,
			googleId: null,
			registerType: REGISTER_TYPE.normal,
			deviceType: "web",
		};

		logIn(body)
			.then((res) => {
				if (res.success === 1) {
					if (res.lgWithOtp) {
						setSignInState(false);
						setResetPassword(true);
						setStep(1);
						setOtp(password);
						setPassword("");
						setLgWithOtp(false);
					} else {
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
	};

	return (
		<div>
			{Tst.Obj}
			{Spn.Obj}
			<div className="nobg">
				<SignInWrapper onSubmit={(e) => e.preventDefault()}>
					{/* {signInState ? ( */}
					<section className="row login mlr-0">
						<div className="container">
							<div className="col-md-6 login-sec">
								<div className="top-head">login to access your account</div>

								<div className="top-head-sub">
									<strong>
										If you’re an existing member and this is your first time
										logging in, you’ll need to claim your account.
									</strong>
									To do so, please click “
									<strong
										className="cursor-pointer"
										onClick={(e) => {
											props.resetForm();
											toggleForgotPassword();
										}}
									>
										Forgot Password
									</strong>
									”. You’ll receive a one-time passcode (OTP) at the email
									address you have on file. Once you receive the OTP, return to
									the login page and sign in using that temporary password to
									claim your account.
								</div>

								<div className="form-sec mb-40">
									<div className="main-head mb-40">LOG IN</div>

									<div className="inp-row mtb-20">
										{step === 1 && (
											<div className="col-11">
												<Input
													//label="Email"
													type="text"
													placeholder="EMAIL"
													id="email"
													//fontSize={"fs-16 text-dark"}
													//contentFontSize={"fs-14"}
													value={email}
													onChange={(e) => setEmail(e.target.value)}
												/>
												<Error field="email" />
											</div>
										)}

										{step === 2 && emailValid && (
											<div className="mb-20 col-12 col-sm-12 col-md-9 col-lg-10 col-xl-10">
												
												<div className="position-relative">
													<Input
														//label={lgWithOtp ? "The OTP has been sent to your email. Please enter it below." : "Password"}
														type={passwordType}
														placeholder={
															lgWithOtp
																? "The OTP has been sent to your email. Please enter it below."
																: "Password"
														}
														id="password"
														//fontSize={"fs-16 text-dark"}
														//contentFontSize={"fs-14"}
														value={password}
														onChange={(e) => setPassword(e.target.value)}
													/>
													{passwordType === "password" ? (
														<i
															className="fa fa-eye eye pwd cursor-pointer"
															onClick={() => {
																setPasswordType("text");
															}}
														></i>
													) : (
														<i
															className="fa fa-eye-slash eye pwd cursor-pointer"
															onClick={() => {
																setPasswordType("password");
															}}
														></i>
													)}
												</div>
												<Error field="password" />
											</div>
										)}
									</div>

									<div className="inp-row mtb-20">
										<div className="mb-20 col-10">
											<span>
												<label
													className="red--text cursor-pointer"
													onClick={(e) => {
														props.resetForm();
														toggleForgotPassword();
													}}
												>
													FORGOT PASSWORD?
												</label>
											</span>
										</div>
									</div>

									<div className="flex-container">
										<Button
											className="button mt-20"
											name={step === 1 ? "CONTINUE" : "LOG IN"}
											clicked={step === 1 ? validateEmail : handleSignIn}
										/>
									</div>
								</div>

								<div className="d-flex justify-content-center">
									<span onClick={handleFacebookLogin}>
										<img
											src={FB}
											alt="Create with Facebook"
											className="mr-20 cursor-pointer"
										/>
									</span>
									<span>
										<span onClick={handleGoogleLogin}>
											<img src={Google} alt="Create with Google" className="cursor-pointer" />
										</span>
									</span>
								</div>

								<div className="sgp-area">
									<div className="sgp-ttl">Don't have an account?</div>
									<div className="sgp-btn-bx">
										<Link to="/signup">Sign Up Now</Link>
									</div>
								</div>
							</div>
						</div>
					</section>
					{/* ) : null} */}
				</SignInWrapper>
			</div>

			<ForgotPassword
				show={setForgotPassword}
				clicked={toggleForgotPassword}
				reset={reset}
			/>
			<ResetPassword
				show={resetPassword}
				clicked={toggleResetPassword}
				reset={reset}
				memberEmail={email}
				otp={otp}
			/>
		</div>
	);
};

export default compose(withRouter, enhancer, connect(null, { login }))(SignIn);
