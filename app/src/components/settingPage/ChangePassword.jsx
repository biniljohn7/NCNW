import React, { useState } from 'react'
import Input from '../../UI/input/input'
import Wrapper from './common.style'
import { changePassword } from '../../api/commonAPI'
import Toast from "../../UI/Toast/Toast";
import Spinner from "../../UI/Spinner/Spinner";
import { useHistory } from 'react-router-dom';

import AuthActions from "../../redux/auth/actions";
import { connect } from "react-redux";
const { login } = AuthActions;

const ChangePassword = (props) => {
	const {
		handleChange,
		handleBlur
	} = props
	const [passwordType, setPasswordType] = useState('password')
	const [curPwd, setCurPasswordType] = useState('password')
	const [reTypPwd, setRePasswordType] = useState('password')
	const [loading, setLoading] = useState(false)
	const [ErrorList, setErrorList] = useState({});
	const history = useHistory();

	let Tst = Toast();
	let Spn = Spinner();

	const Error = ({ field }) => {
		return ErrorList[field] ?
			<div className="text-danger">
				{ErrorList[field]}
			</div> :
			<></>;
	};

	const handleChangePwd = (e) => {
		function el(id) {
			return document.getElementById(id);
		}

		function validatePassword(password) {
			const trimmedPassword = password.trim();
			if (!trimmedPassword) {
				return "Password is required";
			}
			const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$&*]).{8,}$/;
			if (!passwordRegex.test(trimmedPassword)) {
				return "Must Contain 8 Characters, One Uppercase, One Lowercase, One Number and one special case Character";
			}
			return null;
		}

		let
			sErrs = {}

		if (!el('oldPassword').value.trim()) {
			sErrs['oldPassword'] = 'Old password is required';
		}
		let res = validatePassword(el('newPassword').value);
		if (res) {
			sErrs['newPassword'] = res;
		}
		if (!el('confirmPwd').value.trim()) {
			sErrs['confirmPwd'] = 'This field is required';
		} else if (el('confirmPwd').value.trim() !== el('newPassword').value.trim()) {
			sErrs['confirmPwd'] = 'Password didn\'t match';
		}

		setErrorList(sErrs);

		if (Object.keys(sErrs).length < 1) {
			setLoading(true);
			Spn.Show();
			changePassword({
				currentPassword: el('oldPassword').value,
				newPassword: el('newPassword').value,
				confirmPwd: el('confirmPwd').value
			})
				.then((res) => {
					if (res.success === 0) {
						Tst.Error(res.message);
					} else {
						props.login({
							isLogin: false,
							accessToken: null,
						});
						Tst.Success(res.message);
					}
				})
				.catch((err) => {
					console.error(err)
					Tst.Error('Something went wrong!');
				})
				.finally(() => {
					setLoading(false);
					Spn.Hide();
					setTimeout(() => history.replace('/signin'), 800)
				})
		}
	}

	document.title = 'Set Password - ' + window.seoTagLine;

	return (
		<>
			{Tst.Obj}
			{Spn.Obj}
			<Wrapper>
				<section className={props.isMobile ? ' border plr-15 ptb-30' : ''}>
					<h3 className="text-bold">Change Password</h3>
					<form
						className={'mt-20 ' + (window.innerWidth < 768 ? 'wp-100' : 'wp-70')}
					>
						<div className="mb-20">
							<div className="position-relative">
								<Input
									label="Current Password"
									type={curPwd}
									placeholder="Current Password"
									id="oldPassword"
									fontSize={'fs-16 text-dark'}
									contentFontSize="fs-14"
									onChange={handleChange}
									onBlur={handleBlur}
									className="pwd-inp"
								/>

								{curPwd === 'password' ? (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => {
											setCurPasswordType('text')
										}}
									>
										visibility
									</span>
								) : (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => {
											setCurPasswordType('password')
										}}
									>
										visibility_off
									</span>
								)}
							</div>


							<Error field="oldPassword" />
						</div>
						<div className="mb-20">
							<div className="position-relative">
								<Input
									label="New Password"
									type={passwordType}
									placeholder="New Password"
									id="newPassword"
									fontSize={'fs-16 text-dark'}
									contentFontSize={'fs-14'}
									onChange={handleChange}
									onBlur={handleBlur}
									className="pwd-inp"
								/>
								{passwordType === 'password' ? (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => {
											setPasswordType('text')
										}}
									>
										visibility
									</span>
								) : (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => {
											setPasswordType('password')
										}}
									>
										visibility_off
									</span>
								)}
							</div>
							<Error field="newPassword" />
						</div>
						<div className="mb-20">
							<div className="position-relative">
								<Input
								label="Confirm Password"
								type={reTypPwd}
								placeholder="Password"
								id="confirmPwd"
								fontSize={'fs-16 text-dark'}
								contentFontSize={'fs-14'}
								onChange={handleChange}
								onBlur={handleBlur}
								className="pwd-inp"
								/>
							
							{reTypPwd === 'password' ? (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => {
											setRePasswordType('text')
										}}
									>
										visibility
									</span>
								) : (
									<span
										className="material-symbols-outlined eye pwd cursor-pointer"
										onClick={() => {
											setRePasswordType('password')
										}}
									>
										visibility_off
									</span>
								)}
								</div>
							<Error field="confirmPwd" />

						</div>
						<button
							type="button"
							className="btn btn-rounded button plr-30 ptb-10"
							onClick={handleChangePwd}
							disabled={loading}
						>
							SAVE
						</button>
					</form>
				</section>
			</Wrapper>
		</>
	)
}

export default connect(null, { login })(ChangePassword);