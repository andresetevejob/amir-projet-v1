const express = require('express');
const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
const mongoose = require('mongoose')

const host = process.env.HOSTDB || 'localhost';
const port = process.env.PORTDB || 27017;
console.log(host, port);
mongoose.connect(`mongodb://${host}:${port}/nextu?retryWrites=true&w=majority`)
    .then(() => console.log('Connexion à MongoDB réussie !'))
    .catch(() => console.log('Connexion à MongoDB échouée !'));
const Books = require('./book');

app.get('/', (req, res) => {
    res.status(200).send('Hello World!');
});

app.get('/books', (req, res) => {
    Books.find()
        .then(books => res.status(200).json(books))
        .catch(error => res.status(400).json({ error }));
}); // List

app.get('/books/:id', (req, res) => {
    Books.find({ "_id": req.params.id })
        .then(book => res.status(200).json(book))
        .catch(error => res.status(400).json({ error }))
}) // Get

app.post('/books', (req, res) => {
    console.log(req.body);
    const book = new Books({
        ...req.body
    });
    book.save()
        .then(() => res.status(201).json({ message: 'Objet enregistré !' }))
        .catch(error => res.status(400).json({ error }));
}); // add

app.put('/books/:id', (req, res) => {
    Books.updateOne({ _id: req.params.id }, { ...req.body, _id: req.params.id })
        .then(() => res.status(200).json({ message: 'Objet modifié !' }))
        .catch(error => res.status(400).json({ error }));
}); // update

app.delete('/books/:id', (req, res) => {
    Books.deleteOne({ _id: req.params.id })
        .then(() => res.status(200).json({ message: 'Objet supprimé !' }))
        .catch(error => res.status(400).json({ error }));
}); // delete

module.exports = app;