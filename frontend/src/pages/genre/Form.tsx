import * as React from 'react';
import {useEffect, useState} from 'react';
import {Box, Button, ButtonProps, Checkbox, MenuItem, TextField} from "@material-ui/core"
import {makeStyles, Theme} from "@material-ui/core/styles"
import {useForm} from 'react-hook-form';
import genreHttp from "../../util/http/genre-http"
import categoryHttp from "../../util/http/category-http"
import {Category} from "../category/Form"
import * as yup from "../../util/vendor/yup"
import {yupResolver} from "@hookform/resolvers/yup"
import {useSnackbar} from "notistack"
import {useHistory, useParams} from "react-router-dom"

const useStyles = makeStyles((theme: Theme) => {
	return {
		submit: {
			margin: theme.spacing(0.5)
		}
	}
})

type Genre = {
	name: string;
	is_active:boolean;
	categories_id: any[] | any;
}

const validationSchema = yup.object().shape({
	name: yup.string()
		.label('Nome')
		.required()
		.max(255),
	is_active: yup.boolean()
		.label('Ativo?'),
	categories_id: yup.array()
		.label('Categorias')
		.required()
})

const Form = () => {

	const {
		register,
		handleSubmit,
		getValues,
		setValue,
		errors,
		reset,
		watch
	} = useForm<Genre>({
		resolver: yupResolver(validationSchema),
		defaultValues: {
			categories_id: [],
			is_active: true
		},
	});

	const classes = useStyles();
	const snackbar = useSnackbar();
	const history = useHistory();
	const {id} = useParams<{id:string}>();
	const [genre, setGenre] = useState<{id:string} | null>(null);
	const [loading, setLoading] = useState<boolean>(false);
	const [categories, setCategories] = useState<any[]>([]);

	const buttonProps: ButtonProps = {
		className: classes.submit,
		variant: 'contained',
		color: 'secondary',
		disabled: loading
	}

	useEffect(() => {
		async function loadData() {
			setLoading(true);
			const promises = [categoryHttp.list()];

			if(id){
				promises.push(genreHttp.get(id));
			}
			try {
				const [categoriesResponse, genreResponse] = await Promise.all(promises);
				setCategories(categoriesResponse.data.data);

				if(id){
					setGenre(genreResponse.data.data);
					reset({
						...genreResponse.data.data,
						categories_id: genreResponse.data.data.categories.map( (category:any) => category.id)
					});
				}
			}catch (error) {
				console.log(error);
				snackbar.enqueueSnackbar('Não foi possível carregar as informações', {
					variant: 'error',
				});
			}finally {
				setLoading(false);
			}
		}

		loadData();
	}, [snackbar])

	useEffect(()=>{
		register({name:'categories_id'})
	}, [register]);

	async function onSubmit(formData: any, event: any) {
		setLoading(true);

		try {
			const http = !genre
				? genreHttp.create(formData)
				: genreHttp.update(genre.id, formData);
			const {data} = await http;

			snackbar.enqueueSnackbar('Gênero salvo com sucesso',
				{ variant: 'success' }
			);
			setTimeout(()=>{
				event
					? (
						id ? history.replace(`/genres/${data.data.id}/edit`)
							: history.push(`/genres/${data.data.id}/edit`)
					)
					: history.push('/genres')
			});
		}catch (error) {
			console.log(error);
			snackbar.enqueueSnackbar(
				'Não foi possível salvar o gênero',
				{ variant: 'error' }
			);
		}finally {
			setLoading(false);
		}
	}

	return (
		<form onSubmit={handleSubmit(onSubmit)}>
			<TextField
				name="name"
				label="Nome"
				fullWidth
				variant={"outlined"}
				inputRef={register}
				disabled={loading}
				error={errors.name !== undefined}
				helperText={errors.name && errors.name.message}
				InputLabelProps={{shrink: true}}
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
				disabled={loading}
				error={errors.categories_id !== undefined}
				helperText={errors.categories_id && errors.categories_id.message}
				InputLabelProps={{shrink: true}}
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
				<Button {...buttonProps} onClick={()=>onSubmit(getValues(), null)}>Salvar</Button>
				<Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
			</Box>
		</form>
	);
};


export default Form;