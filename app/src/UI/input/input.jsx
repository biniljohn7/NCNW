import React from "react";
import InputWrapper from "./input.style";

const input = (props) => {
	return (
		<InputWrapper>
			{props.label && props.label !== "" ? (
				<div className="">
					<label
						className={["insidelabelmain mb-10", props.fontSize].join(" ")}
					>
						{props.label}
					</label>
					{props.required ? <span className="red--text"> *</span> : null}
				</div>
			) : null}
			<input
				type={props.type}
				placeholder={props.placeholder}
				id={props.id}
				name={props.name}
				onChange={props.onChange}
				onBlur={props.onBlur}
				value={props.value}
				style={props.style}
				autocomplete={props.autocomplete}
				className={[
					"inputmain pa-10",
					props.contentFontSize,
					props.className,
				].join(" ")}
				disabled={props.disabled}
				maxLength={props.maxLength}
				onKeyPress={(e) => {
					if (e.key === "Enter" && props.onEnter) {
						props.onEnter();
					}
				}}
			/>
		</InputWrapper>
	);
};

export default input;
