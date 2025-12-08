import { withFormik } from "formik";
import * as Yup from "yup";

const formikEnhancer = withFormik({
    validationSchema: Yup.object().shape({
        firstName: Yup.string()
            .trim()
            .required("First name is required"),
        lastName: Yup.string()
            .trim()
            .required("Last name is required"),
        email: Yup.string()
            .email("Invalid Email")
            .trim()
            .required("Email is required"),
        phoneNumber: Yup.string()
            .trim()
            .notRequired()
            .test(
                "is-valid-phone",
                "Invalid phone number",
                (value) => {
                    if (!value) {
                        return true;
                    }
                    return (/^[0-9 +()[]\.-_]{6,}$/).test(value);
                }
            ),
        collegiateSection: Yup.string()
            .trim()
            .required(
                "Please choose a section"
            ),
        password: Yup.string()
            .trim()
            .required("This field is required")
            .matches(
                /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$&*]).{8,}$/,
                "This following conditions are not fulfilled"
                // "Must Contain 8 Characters, One Uppercase, One Lowercase, One Number and one special case Character"
            ),
        confirmPwd: Yup.string()
            .oneOf([Yup.ref("password"), null], "Password didn't match")
            .required("This field is required"),
    }),

    handleSubmit: (values) => { },
    displayName: "CustomValidationForm",
    enableReinitialize: true,
    isInitialValid: false,
});

export default formikEnhancer;
