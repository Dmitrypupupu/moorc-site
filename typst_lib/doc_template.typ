// Шаблон документации для МООРС
// Использование: #import "typst_lib/doc_template.typ": *

// Функция для создания заголовка документа
#let doc-header(title, subtitle: none) = {
  align(center)[
    #text(size: 24pt, weight: "bold")[#title]
    
    #if subtitle != none [
      #v(0.5em)
      #text(size: 16pt)[#subtitle]
    ]
    
    #v(2em)
  ]
}

// Функция для создания секции
#let section(title, content) = {
  v(1em)
  text(size: 18pt, weight: "bold")[#title]
  v(0.5em)
  content
  v(0.5em)
}

// Функция для создания подсекции
#let subsection(title, content) = {
  v(0.8em)
  text(size: 14pt, weight: "bold")[#title]
  v(0.3em)
  content
  v(0.3em)
}

// Функция для создания кодового блока
#let code-block(code, lang: none) = {
  block(
    fill: luma(240),
    inset: 10pt,
    radius: 4pt,
    width: 100%,
    [
      #if lang != none [
        #text(size: 9pt, fill: gray)[#lang]
        #v(0.3em)
      ]
      #raw(code, block: true)
    ]
  )
}

// Функция для создания списка возможностей с иконками
#let feature-list(items) = {
  for item in items [
    - #item.icon *#item.title* — #item.description
  ]
}

// Функция для создания таблицы команд
#let command-table(commands) = {
  table(
    columns: 2,
    stroke: 0.5pt,
    inset: 8pt,
    align: left,
    [*Команда*], [*Описание*],
    ..commands.map(cmd => (raw(cmd.command), cmd.description)).flatten()
  )
}

// Функция для создания блока предупреждения
#let warning(content) = {
  block(
    fill: rgb(255, 243, 205),
    inset: 10pt,
    radius: 4pt,
    width: 100%,
    [
      ⚠️ *Внимание:* #content
    ]
  )
}

// Функция для создания блока информации
#let info(content) = {
  block(
    fill: rgb(230, 244, 255),
    inset: 10pt,
    radius: 4pt,
    width: 100%,
    [
      ℹ️ *Информация:* #content
    ]
  )
}

// Основная функция шаблона документа
#let doc(
  title: "",
  subtitle: none,
  author: none,
  date: none,
  body
) = {
  // Настройки документа
  set document(title: title, author: author, date: date)
  set page(
    paper: "a4",
    margin: (x: 2.5cm, y: 3cm),
    numbering: "1",
  )
  set text(
    font: "New Computer Modern",
    size: 11pt,
    lang: "ru"
  )
  set par(justify: true, leading: 0.65em)
  set heading(numbering: "1.")
  
  // Заголовок
  doc-header(title, subtitle: subtitle)
  
  // Информация об авторе и дате
  if author != none or date != none [
    align(center)[
      #if author != none [
        #text(size: 12pt)[#author]
      ]
      #if date != none [
        #v(0.3em)
        #text(size: 10pt, fill: gray)[#date]
      ]
    ]
    #v(2em)
  ]
  
  // Содержимое
  body
}
