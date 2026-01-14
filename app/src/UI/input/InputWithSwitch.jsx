import React from 'react'
import InputWrapper from './input.style'
import Switch from 'react-switch'
import { HEADER_COLOR } from '../../helper/constant'

const input = (props) => {
	return (
		<InputWrapper>
			<div className="position-relative">
				{props.label && props.label !== '' && (
					<label
						className={['insidelabelmain mb-10', props.fontSize].join(' ')}
					>
						{props.label}
					</label>
				)}
				{props.required ? <span className="red--text"> *</span> : null}

				{props.switchPresent && (
					<Switch
						onChange={(checked) => {
							props.switchChange(checked)
						}}
						checked={props.checked}
						onColor="#EAEAEA"
						onHandleColor={HEADER_COLOR}
						handleDiameter={10}
						uncheckedIcon={false}
						checkedIcon={false}
						boxShadow="0px 1px 5px rgba(0, 0, 0, 0.6)"
						activeBoxShadow="0px 0px 1px 10px rgba(0, 0, 0, 0.2)"
						height={15}
						width={40}
						className="profile-switch"
					/>
				)}
			</div>

			<input
				type={props.type}
				placeholder={props.placeholder}
				id={props.id}
				onChange={props.onChange}
				onBlur={props.onBlur}
				//value={props.value}
				defaultValue={props.defaultValue}
				className={[
					'input-switch-main pa-10',
					props.contentFontSize,
					props.className,
				].join(' ')}
				disabled={props.disabled}
				maxLength={props.maxLength}
			/>
		</InputWrapper>
	)
}

export default input
