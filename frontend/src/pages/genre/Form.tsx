import * as React from 'react';
import {useEffect, useState} from 'react';
import {Box, Button, ButtonProps, Checkbox, MenuItem, TextField} from "@material-ui/core"
import {makeStyles, Theme} from "@material-ui/core/styles"
import {useForm} from 'react-hook-form';
import genreHttp from "../../util/http/genre-http"
import categoryHttp from "../../util/http/category-http"

const useStyles = makeStyles((theme: Theme) => {
	return {
		submit: {
			margin: theme.spacing(0.5)
		}
	}
})

const Form = () => {

	const classes = useStyles();

	const buttonProps: ButtonProps = {
		className: classes.submit,
		variant: 'contained',
		color: 'secondary'
	}

	const [categories, setCategories] = useState<any[]>([]);

	const {register, handleSubmit, getValues, setValue, watch} = useForm({
		defaultValues: {
			categories_id: []
		}
	});

	useEffect(()=>{
		register({name:'categories_id'})
	}, [register]);

	useEffect(()=>{
		categoryHttp
			.list()
			.then(({data}) => setCategories(data.data))
	}, []);

	function onSubmit(formData: any) {
		genreHttp
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
			/>
			<TextField
				select
				name="categories_id"
				value={watch('categories_id')}
				label="Categorias"
				margin={"normal"}
				variant={"outlined"}
				fullWidth
				onChange={(e)=>{
					setValue('categories_id', e.target.value);
				}}
				SelectProps={{
					multiple:true
				}}
			>
				<MenuItem value="" disabled>
					<em>Selecione categorias</em>
				</MenuItem>
				{
					categories.map(
						(category, key) => (
							<MenuItem key={key} value={category.id}>{category.name}</MenuItem>
						)
					)
				}
			</TextField>
			<Checkbox
				name={"is_active"}
				inputRef={register}
				defaultChecked
			/> Ativo?
			<Box dir={"rtl"}>
				<Button {...buttonProps} onClick={()=>onSubmit(getValues())}>Salvar</Button>
				<Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
			</Box>
		</form>
	);
};


export default Form;