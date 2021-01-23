import {setLocale} from 'yup';

const ptBR = {

	mixed: {
		// eslint-disable-next-line
		required: '${path} é obrigatório'
	},
	string: {
		// eslint-disable-next-line
		max: '${path} precisa ter no máximo ${max} caracteres',
		// eslint-disable-next-line
		min: '${path} precisa ter no mínimo ${min} caracteres',
	},
	number: {
		// eslint-disable-next-line
		min: '${path} precisa ser no mínimo ${min}',
		// eslint-disable-next-line
		max: '${path} precisa ser no máximo ${min}',
	}
};

setLocale(ptBR);

export * from 'yup';