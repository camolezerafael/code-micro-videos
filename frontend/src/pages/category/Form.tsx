import * as React from 'react';
import {Box, Button, ButtonProps, Checkbox, TextField} from "@material-ui/core"
import {makeStyles, Theme} from "@material-ui/core/styles"
import {useForm} from 'react-hook-form';
import categoryHttp from "../../util/http/category-http"
import * as yup from '../../util/vendor/yup';
import {yupResolver} from "@hookform/resolvers/yup"

const useStyles = makeStyles((theme: Theme) => {
	return {
		submit: {
			margin: theme.spacing(0.5)
		}
	}
});

type Category = {
	name: string;
	description: string;
	is_active:boolean;
}

const validationSchema = yup.object().shape({
	name: yup.string()
		.label('Nome')
		.required(),
});


const Form = () => {

	const classes = useStyles();

	const buttonProps: ButtonProps = {
		className: classes.submit,
		variant: 'contained',
		color: 'secondary'
	}

	const {register, handleSubmit, getValues, errors} = useForm<Category>({
		resolver: yupResolver(validationSchema),
		defaultValues: {
			is_active: true
		},
	});

	function onSubmit(formData: any) {
		categoryHttp
			.create(formData)
			.then((response) => console.log(response));
	}

	return (
		<form onSubmit={handleSubmit(onSubmit)}>
			<TextField
				name="name"
				label="Nome"
				fullWidth
				variant={"outlined"}
				inputRef={register}
				error={errors.name !== undefined}
				helperText={errors.name && errors.name.message}
			/>
			<TextField
				name="description"
				label="Descrição"
				multiline
				rows="4"
				fullWidth
				variant={"outlined"}
				margin={"normal"}
				inputRef={register}
			/>
			<Checkbox
				name={"is_active"}
				color={"primary"}
				inputRef={register}
				defaultChecked
			/>
			Ativo?
			<Box dir={"rtl"}>
				<Button {...buttonProps} onClick={()=>onSubmit(getValues())}>Salvar</Button>
				<Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
			</Box>
		</form>
	);
};


export default Form;