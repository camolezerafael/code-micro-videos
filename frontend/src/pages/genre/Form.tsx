import * as React from 'react';
import {useEffect, useState} from 'react';
import {Checkbox, MenuItem, TextField} from "@material-ui/core"
import {useForm} from 'react-hook-form';
import genreHttp from "../../util/http/genre-http"
import categoryHttp from "../../util/http/category-http"
import * as yup from "../../util/vendor/yup"
import {yupResolver} from "@hookform/resolvers/yup"
import {useSnackbar} from "notistack"
import {useHistory, useParams} from "react-router-dom"
import {Category, Genre} from "../../util/models"
import SubmitActions from "../../components/SubmitActions"
import {DefaultForm} from "../../components/DefaultForm"

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
		watch,
		trigger
	} = useForm<Genre | any>({
		resolver: yupResolver(validationSchema),
		defaultValues: {
			categories_id: [],
			is_active: true
		},
	});

	const snackbar = useSnackbar();
	const history = useHistory();
	const {id} = useParams<{id:string}>();
	const [genre, setGenre] = useState<Genre | null>(null);
	const [categories, setCategories] = useState<Category[]>([]);
	const [loading, setLoading] = useState<boolean>(false);

	useEffect(() => {
		let isSubscribed = true;
		(async () => {
			setLoading(true);
			const promises = [ categoryHttp.list({ queryParams:{ all: '' } } ) ];

			if(id){
				promises.push(genreHttp.get(id));
			}
			try {
				const [categoriesResponse, genreResponse] = await Promise.all(promises);
				if(isSubscribed) {
					setCategories(categoriesResponse.data.data);

					if (id) {
						setGenre(genreResponse.data.data);
						const categories_id = genreResponse.data.data.categories.map((category: any) => category.id)
						reset({
							...genreResponse.data.data,
							categories_id
						});
					}
				}
			}catch (error) {
				console.error(error);
				snackbar.enqueueSnackbar('Não foi possível carregar as informações', {
					variant: 'error',
				});
			}finally {
				setLoading(false);
			}
		})()

		return () => {
			isSubscribed = false
		}

	}, [id, reset, snackbar])

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
			console.error(error);
			snackbar.enqueueSnackbar(
				'Não foi possível salvar o gênero',
				{ variant: 'error' }
			);
		}finally {
			setLoading(false);
		}
	}

	return (
		<DefaultForm GridItemProps={{xs:12, md:6}} onSubmit={handleSubmit(onSubmit)}>
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
			<SubmitActions
				disabledButtons={loading}
				handleSave={ () =>
					trigger().then(isValid => {
						isValid && onSubmit(getValues(), null)
					})
				}
			/>
		</DefaultForm>
	);
};


export default Form;